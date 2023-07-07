<?php

namespace App\Controller\User;

use App\Entity\Auth\User;
use App\Form\User\UserType;
use App\Repository\Auth\UserRepository;
use App\Service\Utils\VerifyUniqueUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class UserController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
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

                $userRepository->add($user, true);

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
}