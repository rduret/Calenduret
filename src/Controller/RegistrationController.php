<?php

namespace App\Controller;

use App\Entity\Auth\User;
use App\Form\RegistrationFormType;
use App\Service\Utils\CaptchaVerify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, CaptchaVerify $captchaVerify): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Vérification du captcha
            if($captchaVerify->checkCaptcha($request->get('g-recaptcha'))){
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
    
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email
    
                $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
                $tokenStorage->setToken($token);
    
                return $this->redirectToRoute('home_user');
            } else {
                $this->addFlash('error', 'Problème de recaptcha');
                return $this->redirectToRoute('app_register');
            }

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
