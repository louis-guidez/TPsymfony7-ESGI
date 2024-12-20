<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Service\DistanceCalculator;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParticipantController extends AbstractController
{

    private $entityManager;
    private $requestStack;
    private $geoCoder;
    private $distanceCalculator;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, DistanceCalculator $distanceCalculator, GeocodingService $geoCoder)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->geoCoder = $geoCoder;
        $this->distanceCalculator = $distanceCalculator;
    }

    #[Route('/events/{eventId}/participants/new', name: 'addParticipant')]
    public function addParticipant(Request $req, $eventId): Response
    {
        $participant = new Participant();
        $event = $this->entityManager->getRepository(Event::class)->find($eventId);
        $participant->setEvent($event);

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setEvent($event);
            $existingParticipant = $this->entityManager->getRepository(Participant::class)->findOneBy([
                'name' => $participant->getName(),
                'email' => $participant->getEmail(),
                'event' =>$participant->getEvent(),
            ]);

            if ($existingParticipant) {
                $this->addFlash('error', 'Ce participant est déja inscrit à cet événement.');
            } else {

                $coordinateEvent = $this->geoCoder->getCoordinates($event->getCity());
                $coordinateParticipant = $this->geoCoder->getCoordinates($participant->getCity());

                if ($coordinateEvent === null || $coordinateParticipant === null) {
                    $this->addFlash('error', 'Villes inconnues, impossible de calculer la distance');
                } else {

                    $distance = $this->distanceCalculator->calculateDistance(
                        $coordinateEvent['latitude'],
                        $coordinateEvent['longitude'],
                        $coordinateParticipant['latitude'],
                        $coordinateParticipant['longitude']
                    );

                    $participant->setDistanceToEvent($distance);
                }


                $this->entityManager->persist($participant);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre participant a été ajouté avec succès.');
                return $this->redirectToRoute('viewEvent', ['id' => $eventId]);
            }
        }

        return $this->render('pages/participants/new2.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }
    #[Route('/new_participant', name: 'new_participant')]
    public function new_participant(Request $req): Response
    {
        $participant = new Participant();

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingParticipant = $this->entityManager->getRepository(Participant::class)->findOneBy([
                'name' => $participant->getName(),
                'email' => $participant->getEmail(),
                'event' =>$participant->getEvent(),
            ]);

            if ($existingParticipant) {
                $this->addFlash('error', 'Ce participant est déja inscrit à ces événements.');
            } else {
                $this->entityManager->persist($participant);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre participant a été ajouté avec succès.');
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('pages/participants/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit_participant/{id}', name: 'edit_participant')]
    public function edit_participant(Request $req, $id): Response
    {
        $participant = $this->entityManager->getRepository(Participant::class)->find($id);

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingParticipant = $this->entityManager->getRepository(Participant::class)->findOneBy([
                'name' => $participant->getName(),
                'email' => $participant->getEmail(),
                'event' => $participant->getEvent()
            ]);

            if ($existingParticipant && $existingParticipant != $participant) {
                $this->addFlash('error', 'Ce participant existe déja et/ou est déja inscrit à ces evenement.');
            } else {
                $this->entityManager->persist($participant);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre participant a été modifié avec succès.');
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('pages/participants/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }
}
