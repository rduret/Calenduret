<?php

namespace App\Controller\User;

use App\Entity\Auth\User;
use App\Form\User\UserType;
use App\Repository\Auth\UserRepository;
use App\Service\Utils\VerifyUniqueUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class UserController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $user = $this->getUser();
        
        if ($user !== null) {
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('home_admin');
            } else {
                return $this->redirectToRoute('home_user');
            }
        }

        return $this->render('front/home.html.twig', [
            'transparentNavbar' => true
        ]);
    }

    #[Route('/dashboard', name: 'home_user')]
    public function dashboard(): Response
    {
        return $this->render('front/dashboard.html.twig');
    }

    #[Route('admin/utilisateurs/index', name: 'user_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByRole('ROLE_USER');

        return $this->render('user/index.html.twig', [
            'user_type' => 'user',
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/new', name: 'user_new')]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, VerifyUniqueUser $verifyUniqueUser, UserRepository $userRepository): Response
    {
        if($this->isCsrfTokenValid('admin', (string) $request->request->get('user_token'))) {
            $user = new User();
            $user->setRoles(["ROLE_ADMIN"]);
            $userToken = 'admin';
        } elseif($this->isCsrfTokenValid('user', (string) $request->request->get('user_token'))) {
            $user = new User();
            $user->setRoles(["ROLE_USER"]);
            $userToken = 'user';
        } else {
            $this->addFlash('error', 'Impossible de créer un utilisateur de ce type');
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($verifyUniqueUser->verifyUniqueUser($user->getEmail(), $user)) {
                if ($user->getPlainPassword() !== null) {
                    $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                    $user->setPassword($password);
                }

                $userRepository->save($user, true);

                $this->addFlash('success','L\'utilisateur a bien été créé');

                if($user->hasRole("ROLE_ADMIN")) {
                    return $this->redirectToRoute('admin_index');
                } else {
                    return $this->redirectToRoute('user_index');
                }
            } else {
                $this->addFlash('error', 'Cet email est déjà utilisé par un autre utilisateur');
            }
        }

        return $this->render('user/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'user_token' => $userToken
        ]);
    }


    #[Route('/admin/user/edit', name: 'user_edit', methods: "POST")]
    public function edit(Request $request, UserProviderInterface $userProvider, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger, VerifyUniqueUser $verifyUniqueUser, EntityManagerInterface $entityManager): Response
    {
        if($this->isCsrfTokenValid('edit_'.$request->request->get('identifier'), (string) $request->request->get('edit_token'))) {
            try {
                /** @var User $user */
                $user = $userProvider->loadUserByIdentifier((string) $request->request->get('identifier'));
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if ($verifyUniqueUser->verifyUniqueUser($user->getEmail(), $user)) {
                        if ($user->getPlainPassword() !== null) {
                            $password = $passwordHasher->hashPassword($user, $user->getPlainPassword());
                            $user->setPassword($password);
                        }

                        $entityManager->flush();

                        $this->addFlash(
                            'success',
                            'L\'utilisateur a bien été modifié',
                        );

                        return $this->redirectToRoute('user_index');
                    } else {
                        $this->addFlash(
                            'error',
                            'Cet email est déjà utilisé par un autre utilisateur'
                        );
                    }
                }

                return $this->render('user/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'identifier' => $request->request->get('identifier')
                ]);

            } catch (UserNotFoundException $e) {
                $this->addFlash(
                    'success',
                    'L\'utilisateur n\'a pas pu être modifié',
                );
            }


        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @param Request $request
     * @param UserProviderInterface $userProvider
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    #[Route('/admin/user/delete', name: 'user_delete', methods: "POST")]
    public function delete(Request $request, UserProviderInterface $userProvider, UserRepository $userRepository): RedirectResponse
    {

        if($this->isCsrfTokenValid('delete_'.$request->request->get('identifier'), (string) $request->request->get('_token'))) {
            try {
                /** @var User $user */
                $user = $userProvider->loadUserByIdentifier((string) $request->request->get('identifier'));

                $userRepository->remove($user, true);

                $this->addFlash(
                    'success',
                    'L\'utilisateur a bien été supprimé',
                );
            } catch (UserNotFoundException $e) {

                $this->addFlash(
                    'success',
                    'L\'utilisateur n\'a pas pu être supprimé'
                );
            }
        }
        return $this->redirectToRoute('user_index');
    }
}