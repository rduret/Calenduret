<?php

namespace App\Controller\User;

use App\Repository\Auth\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'home_admin')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/administrateurs/index', name: 'admin_index')]
    public function index(UserRepository $userRepository): Response
    {
        $admins = $userRepository->findByRole('ROLE_ADMIN');

        return $this->render('user/index.html.twig', [
            'user_type' => 'admin',
            'users' => $admins,
        ]);
    }
}