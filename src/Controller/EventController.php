<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\DistanceCalculator;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{

    private $entityManager;
    private $requestStack;
    private $distanceCalculator;
    private $geoCoder;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, DistanceCalculator $distanceCalculator, GeocodingService $geocodingService)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->geoCoder = $geocodingService;
        $this->distanceCalculator = $distanceCalculator;
    }


    #[Route('/events', name: 'listEvents')]
    public function listEvents(): Response
    {
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        return $this->render('pages/events/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/{id}', name: 'viewEvent')]
    public function viewEvent($id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        return $this->render('pages/events/view.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/new_event', name: 'new_event')]
    public function new_event(Request $req): Response
    {
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEvent = $this->entityManager->getRepository(Event::class)->findOneBy([
                'name' => $event->getName(),
                'date' => $event->getDate(),
                'city' => $event->getCity()
            ]);

            if ($existingEvent) {
                $this->addFlash('error', 'Cet event existe déja.');
            } else {
                $this->entityManager->persist($event);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre event a été ajouté avec succès.');
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('pages/events/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/calculateDistance/{cityParticipant}/{cityEvent}', name: 'CalculateDistance')]
    public function calculateDistanceToEvent(string $cityParticipant, string $cityEvent): Response
    {
        $coordinateEvent = $this->geoCoder->getCoordinates($cityEvent);
        $coordinateParticipant = $this->geoCoder->getCoordinates($cityParticipant);

        if ($coordinateEvent === null || $coordinateParticipant === null) {
            return new Response('Unable to fetch coordinates for one or both cities.', Response::HTTP_BAD_REQUEST);
        }

        $distance = $this->distanceCalculator->calculateDistance(
            $coordinateEvent['latitude'],
            $coordinateEvent['longitude'],
            $coordinateParticipant['latitude'],
            $coordinateParticipant['longitude']
        );

        return new Response(sprintf('The distance between %s and %s is %.2f km.', $cityParticipant, $cityEvent, $distance));
    }

    #[Route('/edit_event/{id}', name: 'edit_event')]
    public function edit_event(Request $req, $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingEvent = $this->entityManager->getRepository(Event::class)->findOneBy([
                'name' => $event->getName(),
                'date' => $event->getDate(),
                'city' => $event->getCity()
            ]);

            if ($existingEvent && $existingEvent != $event) {
                $this->addFlash('error', 'Cet event existe déja.');
            } else {
                $this->entityManager->persist($event);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre event a été modifié avec succès.');
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('pages/events/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
