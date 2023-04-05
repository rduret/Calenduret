<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_auth_user')]
    public function index(): Response
    {
        return $this->render('auth/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
