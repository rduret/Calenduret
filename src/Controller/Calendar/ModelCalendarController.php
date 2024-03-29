<?php

namespace App\Controller\Calendar;

use Symfony\Component\Uid\Uuid;
use App\Entity\Calendar\Calendar;
use App\Entity\Calendar\ModelBox;
use App\Service\Utils\UploadHandler;
use App\Entity\Calendar\ModelCalendar;
use App\Form\Calendar\ModelCalendarType;
use function PHPUnit\Framework\fileExists;
use App\Form\Calendar\ModelCalendarBoxesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Calendar\ModelBoxRepository;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Calendar\ModelCalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ModelCalendarController extends AbstractController
{
    #[Route('/admin/calendriers/index', name: 'model_calendar_index', methods: ['GET'])]
    public function index(ModelCalendarRepository $modelCalendarRepository): Response
    {
        return $this->render('calendar/model_calendar/index.html.twig', [
            'models' => $modelCalendarRepository->findAll(),
        ]);
    }

    #[Route('/admin/calendriers/new', name: 'model_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ModelCalendarRepository $modelCalendarRepository, UploadHandler $uploadHandler): Response
    {
        $modelCalendar = new ModelCalendar();
        $form = $this->createForm(ModelCalendarType::class, $modelCalendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
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

                    return $this->render('calendar/model_calendar/new.html.twig', [
                        'model' => $modelCalendar,
                        'form' => $form,
                    ]);
                }
            }

            $modelCalendar->setUser($this->getUser());

            $modelCalendarRepository->save($modelCalendar, true);

            $this->addFlash(
                'success',
                'Le modèle a bien été créé'
            );

            return $this->redirectToRoute('model_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/model_calendar/new.html.twig', [
            'model' => $modelCalendar,
            'form' => $form,
        ]);
    }

    #[Route('/admin/calendriers/{id}', name: 'model_calendar_show', methods: ['GET'])]
    public function show(ModelCalendar $modelCalendar): Response
    {
        return $this->render('calendar/model_calendar/show.html.twig', [
            'model' => $modelCalendar,
        ]);
    }

    #[Route('/admin/calendriers/{id}/edit', name: 'model_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ModelCalendar $modelCalendar, ModelCalendarRepository $modelCalendarRepository, UploadHandler $uploadHandler): Response
    {
        $form = $this->createForm(ModelCalendarType::class, $modelCalendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

                    return $this->render('calendar/model_calendar/edit.html.twig', [
                        'model' => $modelCalendar,
                        'form' => $form,
                    ]);
                }
            }

            $modelCalendarRepository->save($modelCalendar, true);

            $this->addFlash(
                'success',
                'Le modèle a bien été édité'
            );

            return $this->redirectToRoute('model_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/model_calendar/edit.html.twig', [
            'model' => $modelCalendar,
            'form' => $form,
        ]);
    }

    #[Route('/admin/calendriers/{id}/cases', name: 'model_calendar_edit_boxes', methods: ['GET', 'POST'])]
    public function editBoxes(Request $request, ModelCalendar $modelCalendar, ModelCalendarRepository $modelCalendarRepository, UploadHandler $uploadHandler): Response
    {

        $form = $this->createForm(ModelCalendarBoxesType::class, $modelCalendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formsModelBoxes = $form->get('modelBoxes');

            foreach ($formsModelBoxes as $formModelBox) {
                $file = $formModelBox->get('file')->getData();
                $modelBox = $formModelBox->getData();

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
    
                        return $this->redirectToRoute('model_calendar_index', [], Response::HTTP_SEE_OTHER);
                    }
                }
            }


            $modelCalendarRepository->save($modelCalendar, true);

            $this->addFlash(
                'success',
                'Les cases ont bien été éditées'
            );

            return $this->redirectToRoute('model_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/model_calendar/edit_boxes.html.twig', [
            'model' => $modelCalendar,
            'form' => $form
        ]);
    }

    #[Route('/admin/calendriers/{id}', name: 'model_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, ModelCalendar $modelCalendar, ModelCalendarRepository $modelCalendarRepository): Response
    {

        if ($this->isCsrfTokenValid('delete_'.$modelCalendar->getId(), $request->request->get('_token'))) {
            if(file_exists($modelCalendar->getPath())){
                unlink($modelCalendar->getPath());
            }
            $modelCalendarRepository->remove($modelCalendar, true);
        }

        $this->addFlash(
            'success',
            'Le modèle a bien été supprimé'
        );

        return $this->redirectToRoute('model_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/calendriers/{id}/change-state', name: 'model_calendar_change_state', methods: ['GET'])]
    public function changeState(ModelCalendar $modelCalendar, ModelCalendarRepository $modelCalendarRepository): Response
    {
        if($modelCalendar->isPublished()){
            $modelCalendar->setIsPublished(false);
            $this->addFlash(
                'success',
                'Le modèle a bien été dépublié'
            );
        } else {
            $modelCalendar->setIsPublished(true);
            $this->addFlash(
                'success',
                'Le modèle a bien été publié'
            );
        }

        $modelCalendarRepository->save($modelCalendar, true);

        return $this->redirectToRoute('model_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/calendriers/previewBox', name: 'model_calendar_preview', methods: ['GET'])]
    public function previewModelBoxModal(Request $request, ModelBoxRepository $modelBoxRepository): JsonResponse
    {
        $uuid = $request->get('uuid');
        $modelBox = $modelBoxRepository->findOneBy(['uuid' => $uuid]);

        $htmlContent = $this->renderView('calendar/model_calendar/preview/preview.html.twig', [
            'modelBox' => $modelBox,
        ]);

        return new JsonResponse($htmlContent);
    }

    #[Route('/calendriers/share', name: 'model_calendar_share', methods: ['GET'])]
    public function shareModal(Request $request, ModelCalendarRepository $modelCalendarRepository, EntityManagerInterface $em): JsonResponse
    {
        $uuid = $request->get('uuid');
        $modelCalendar = $modelCalendarRepository->findOneBy(['uuid' => $uuid]);

        if(($modelCalendar->getUser() === $this->getUser()) || (in_array('ROLE_ADMIN', $this->getUser()->getRoles()))){
            // Générer le calendrier et retourner son uuid
            $newCalendar = new Calendar();
            $newCalendar->setModelCalendar($modelCalendar);
            $em->persist($newCalendar);
            $em->flush();
            $response = $this->generateUrl('calendar_show', [
                'uuid' => $newCalendar->getUuid()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $response = "Vous n'avez pas les droits d'accès";
        }

        return new JsonResponse($response);
    }
}
