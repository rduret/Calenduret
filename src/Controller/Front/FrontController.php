<?php

namespace App\Controller\Front;

use App\Service\Utils\UploadHandler;
use App\Entity\Calendar\ModelCalendar;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Calendar\FrontModelCalendarType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Calendar\ModelCalendarRepository;
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

    #[Route('/mon-espace/calendriers/{page}', name: 'home_user')]
    public function dashboard(ModelCalendarRepository $modelCalendarRepository,  PaginatorInterface $paginator, int $page = 1): Response
    {
        $user = $this->getUser();

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->redirectToRoute('home_admin');
        }

        $modelCalendarsQuery = $modelCalendarRepository->findBy(['user' => $user]);

        $modelCalendars = $paginator->paginate($modelCalendarsQuery, $page, 2, [
            'wrap-queries' => true
        ]);

        return $this->render('front/dashboard.html.twig', [
            'modelCalendars' => $modelCalendars,
        ]);
    }

    #[Route('/mon-espace/calendriers/{uuid}/edit', name: 'user_model_calendar_edit')]
    public function edit(Request $request, UploadHandler $uploadHandler, ModelCalendarRepository $modelCalendarRepository, string $uuid): Response
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

            foreach ($formsModelBoxes as $formModelBoxes) {
                $file = $formModelBoxes->get('file')->getData();
                $modelBox = $formModelBoxes->getData();

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

            $modelCalendarRepository->save($modelCalendar, true);

            $this->addFlash(
                'success',
                'Le modèle a bien été édité'
            );

            return $this->render('front/modelCalendar/edit.html.twig', [
                'model' => $modelCalendar,
                'form' => $form
            ]);
        }

        return $this->render('front/modelCalendar/edit.html.twig', [
            'model' => $modelCalendar,
            'form' => $form
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
