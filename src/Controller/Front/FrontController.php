<?php

namespace App\Controller\Front;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Calendar\ModelCalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/mon-espace/calendriers/{page}', name: 'home_user')]
    public function dashboard(ModelCalendarRepository $modelCalendarRepository,  PaginatorInterface $paginator, int $page = 1): Response
    {
        $user = $this->getUser();
        $modelCalendarsQuery = $modelCalendarRepository->findBy(['user' => $user]);

        $modelCalendars = $paginator->paginate($modelCalendarsQuery, $page, 5, [
            'wrap-queries' => true
        ]);

        return $this->render('front/dashboard.html.twig', [
            'modelCalendars' => $modelCalendars,
        ]);
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
