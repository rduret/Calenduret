<?php

namespace App\Controller\Front;

use App\Entity\Calendar\Box;
use App\Entity\Calendar\Calendar;
use App\Service\Utils\UploadHandler;
use App\Entity\Calendar\ModelCalendar;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Calendar\FrontModelCalendarType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Calendar\ModelCalendarRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

    #[Route('/mon-espace/calendriers/{page}', name: 'home_user', requirements: ['page' => '\d+'])]
    public function dashboard(ModelCalendarRepository $modelCalendarRepository,  PaginatorInterface $paginator, int $page = 1): Response
    {
        $user = $this->getUser();

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->redirectToRoute('home_admin');
        }

        $modelCalendarsQuery = $modelCalendarRepository->findBy(['user' => $user]);

        $modelCalendars = $paginator->paginate($modelCalendarsQuery, $page, 16, [
            'wrap-queries' => true
        ]);

        return $this->render('front/dashboard.html.twig', [
            'modelCalendars' => $modelCalendars,
        ]);
    }

    #[Route('/mon-espace/calendriers/creation', name: 'user_model_calendar_new')]
    public function new(Request $request, UploadHandler $uploadHandler, ModelCalendarRepository $modelCalendarRepository): Response
    {
        $modelCalendar = new ModelCalendar();
        $form = $this->createForm(FrontModelCalendarType::class, $modelCalendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Gestion upload de l'image principal du calendrier
            $file = $form->get('file')->getData();
            if ($file !== null) {
                try {
                    $newFilename = $uploadHandler->uploadFile($file, $request->get('cropped-file'));
                    $modelCalendar->setPath($newFilename);
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Une erreur s\'est produite lors de l\'upload'
                    );

                    return $this->render('front/modelCalendar/new.html.twig', [
                        'model' => $modelCalendar,
                        'form' => $form
                    ]);
                }
            }

            //Gestion upload des nouveaux fichier liés aux cases
            $formsModelBoxes = $form->get('modelBoxes');

            foreach ($formsModelBoxes as $formModelBoxes) {
                $file = $formModelBoxes->get('file')->getData();
                $modelBox = $formModelBoxes->getData();

                if ($file !== null) {
                    try {
                        $newFilename = $uploadHandler->uploadFile($file);
                        $mimeType = $file->getClientMimeType();
                        $modelBox->setName(substr($file->getClientOriginalName(), 0, 50));
                        $modelBox->setType(explode('/', $mimeType)[0]);
                        $modelBox->setPath($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash(
                            'error',
                            'Une erreur s\'est produite lors de l\'upload d\'un des fichiers'
                        );
    
                        return $this->render('front/modelCalendar/new.html.twig', [
                            'model' => $modelCalendar,
                            'form' => $form
                        ]);
                    }
                }
            }

            $modelCalendar->setUser($this->getUser());

            $modelCalendarRepository->save($modelCalendar, true);

            $this->addFlash(
                'success',
                'Le calendrier a bien été créé.'
            );

            return $this->redirectToRoute('home_user');
        }

        return $this->render('front/modelCalendar/new.html.twig', [
            'model' => $modelCalendar,
            'form' => $form
        ]);
    }

    #[Route('/mon-espace/calendriers/{uuid}/edit', name: 'user_model_calendar_edit')]
    public function edit(Request $request, UploadHandler $uploadHandler, ModelCalendarRepository $modelCalendarRepository, string $uuid, EntityManagerInterface $em): Response
    {
        $modelCalendar = $modelCalendarRepository->findOneBy(['uuid' => $uuid]);

        $user = $this->getUser();
        
        if($modelCalendar->getUser() !== $user){
            $this->addFlash(
                'error',
                'Vous n\'avez pas accès à ce calendrier'
            );
            return $this->redirectToRoute('home_user');
        }

        $form = $this->createForm(FrontModelCalendarType::class, $modelCalendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Gestion upload de l'image principal du calendrier
            $file = $form->get('file')->getData();
            if ($file !== null) {
                try {
                    $newFilename = $uploadHandler->uploadFile($file, $request->get('cropped-file'));

                    if(file_exists($modelCalendar->getPath())){
                        unlink($modelCalendar->getPath());
                    }

                    $modelCalendar->setPath($newFilename);
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Une erreur s\'est produite lors de l\'upload'
                    );

                    return $this->render('front/modelCalendar/edit.html.twig', [
                        'model' => $modelCalendar,
                        'form' => $form
                    ]);
                }
            }

            //Gestion upload des nouveaux fichier liés aux cases
            $formsModelBoxes = $form->get('modelBoxes');
            dump($form, $modelCalendar);

            foreach ($formsModelBoxes as $formModelBoxes) {
                $file = $formModelBoxes->get('file')->getData();
                $modelBox = $formModelBoxes->getData();
                dump($modelBox);

                if ($file !== null) {
                    try {
                        $newFilename = $uploadHandler->uploadFile($file);

                        //Si un fichier était relié à la case, on le supprime
                        if(file_exists($modelBox->getPath())){
                            unlink($modelBox->getPath());
                        }

                        $mimeType = $file->getClientMimeType();

                        $modelBox->setName(substr($file->getClientOriginalName(), 0, 50));
                        $modelBox->setType(explode('/', $mimeType)[0]);
                        $modelBox->setPath($newFilename);

                    } catch (FileException $e) {
                        $this->addFlash(
                            'error',
                            'Une erreur s\'est produite lors de l\'upload'
                        );
    
                        return $this->render('front/modelCalendar/edit.html.twig', [
                            'model' => $modelCalendar,
                            'form' => $form
                        ]);
                    }
                }
            }

            //exit;

            $modelCalendarRepository->save($modelCalendar, true);

            $this->addFlash(
                'success',
                'Le modèle a bien été édité'
            );

            return $this->redirectToRoute('user_model_calendar_edit', [
                'uuid' => $modelCalendar->getUuid(),
            ]);
        }

        return $this->render('front/modelCalendar/edit.html.twig', [
            'model' => $modelCalendar,
            'form' => $form
        ]);
    }

    #[Route('/mon-espace/calendriers/{uuid}/delete', name: 'user_model_calendar_delete')]
    public function delete(ModelCalendarRepository $modelCalendarRepository, string $uuid): Response
    {
        $user = $this->getUser();
        $modelCalendar = $modelCalendarRepository->findOneBy(['uuid' => $uuid]);

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->redirectToRoute('home_admin');
        }

        //Suppression du calendrier
        if($modelCalendar !== null && $modelCalendar->getUser() === $user) {
            $modelCalendarRepository->remove($modelCalendar, true);
            $this->addFlash(
                'success',
                'Le modèle de calendrier a bien été supprimé.'
            );
        } else{
            $this->addFlash(
                'error',
                'Erreur lors de la suppression du calendrier.'
            );
        }

        return $this->redirectToRoute('home_user');
    }

    #[Route('/mon-espace/calendriers/{uuid}/change-state', name: 'user_model_calendar_change_state')]
    public function changeState(ModelCalendarRepository $modelCalendarRepository, string $uuid, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $modelCalendar = $modelCalendarRepository->findOneBy(['uuid' => $uuid]);

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->redirectToRoute('home_admin');
        }

        //Changement état du calendrier
        if($modelCalendar !== null && $modelCalendar->getUser() === $user) {
            if($modelCalendar->isPublished()){
                $modelCalendar->setIsPublished(false);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Le calendrier a bien été dépublié.'
                );
            } else {
                $modelCalendar->setIsPublished(true);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Le calendrier a bien été publié.'
                );
            }
        } else{
            $this->addFlash(
                'error',
                'Erreur lors du changement d\'état du calendrier.'
            );
        }

        return $this->redirectToRoute('user_model_calendar_edit', [
            'uuid' => $modelCalendar->getUuid(),
        ]);
    }

    #[Route('calendrier/{uuid}', name: 'calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar, EntityManagerInterface $em): Response
    {
        if ($calendar !== null && $calendar->getModelCalendar()->isPublished()) {
            //Récupération des nouvelles cases
            $calendarBoxes = [];    //Modèle des cases associées au calendrier 
            $modelBoxes = [];       //Modèle des cases associées au MODELE du calendrier 
            $calendar->setIsActive(true);

            foreach ($calendar->getBoxes() as $box) {
                $calendarBoxes[] = $box->getModelBox();
            }

            foreach($calendar->getModelCalendar()->getModelBoxes() as $modelBox) {
                $modelBoxes[] = $modelBox;
            }

            $newModelBoxes = array_diff($modelBoxes, $calendarBoxes);

            foreach ($newModelBoxes as $newModelBox) {
                $newBox = new Box();
                $newBox->setCalendar($calendar);
                $newBox->setModelBox($newModelBox);
                $em->persist($newBox);
            }

            $em->flush();
            $em->refresh($calendar);

            return $this->render('calendar/calendar/show.html.twig', [
                'calendar' => $calendar,
            ]);
        } else {
            $this->addFlash(
                'error',
                'Ce calendrier n\'est plus disponible'
            );
            return $this->redirectToRoute('home');
        }
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
