<?php

// src/Controller/BookingController.php
namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Service;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{


    #[Route('/booking/{idService}', name: 'booking')]
    public function reserve(Request $request, EntityManagerInterface $entityManager, Security $security, $idService): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $service = $entityManager->getRepository(Service::class)->find($idService);

        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingBooking = $entityManager->getRepository(Booking::class)->findOneBy([
                'service' => $service,
                'date' => $booking->getDate(),
                'time' => $booking->getTime()
            ]);

            if ($existingBooking) {
                $this->addFlash('error', 'Ce créneau pour ce service à cette date et à cette heure est déjà réservé par vous ou par une autre personne.');

                return $this->redirectToRoute('booking', ['idService' => $service->getId()]);
            }

            $booking->setUser($this->getUser());
            $booking->setService($service);

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été effectuée avec succès.');
            return $this->redirectToRoute('booking', ['idService' => $service->getId()]);
        }

        // Récupérer les réservations existantes pour le service
        $existingBookings = $entityManager->getRepository(Booking::class)
            ->findBy(['service' => $service]);

        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
            'existingBookings' => $existingBookings,
        ]);
    }

    #[Route('/bookingsUser', name: 'bookings_user')]
    public function bookingsUser(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $bookings = $entityManager->getRepository(Booking::class)->findBy([
            'user' => $user,
        ]);

        return $this->render('booking/bookings.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/booking/{id}/edit', name: 'booking_edit')]
    public function editBooking(Request $request, EntityManagerInterface $entityManager, BookingRepository $bookingRepository, int $id): Response
    {
        $booking = $bookingRepository->find($id);
        if (!$booking) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingBooking = $entityManager->getRepository(Booking::class)->findOneBy([
                'service' => $booking->getService(),
                'date' => $booking->getDate(),
                'time' => $booking->getTime()
            ]);

            if ($existingBooking) {
                $this->addFlash('error', 'Ce créneau pour ce service à cette date et à cette heure est déjà réservé par vous ou par une autre personne.');

                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été modifiée avec succès.');
            return $this->redirectToRoute('bookings_user');
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'service' => $booking->getService(),
            'form' => $form,
        ]);
    }

    #[Route('/booking/{id}/delete', name: 'booking_delete')]
    public function deleteBooking(EntityManagerInterface $entityManager, BookingRepository $bookingRepository, int $id): Response
    {
        $booking = $bookingRepository->find($id);
        if (!$booking) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation supprimée avec succès.');

        return $this->redirectToRoute('bookings_user');
    }

}
