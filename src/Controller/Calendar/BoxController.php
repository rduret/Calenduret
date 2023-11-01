<?php

namespace App\Controller\Calendar;

use App\Entity\Calendar\Box;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoxController extends AbstractController
{
    #[Route('/calendar/box/{uuid}/open', name: 'box_open')]
    public function openBox(Box $box, EntityManagerInterface $em): JsonResponse
    {
       $box->setIsOpen(true);
       $em->flush();

       return new JsonResponse('Case mise à jour avec succès');
    }
}
