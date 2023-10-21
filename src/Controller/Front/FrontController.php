<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
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
            'isHome' => true
        ]);
    }

    #[Route('/mon-espace', name: 'home_user')]
    public function dashboard(): Response
    {
        return $this->render('front/dashboard.html.twig');
    }

    #[Route('/pages/condition-generales', name: 'page_cgu')]
    public function cgu(): Response
    {
        return $this->render('front/pages/cgu.html.twig');
    }

    #[Route('/pages/mentions-legales', name: 'page_mentions')]
    public function mentions(): Response
    {
        return $this->render('front/pages/mentions.html.twig');
    }
}
