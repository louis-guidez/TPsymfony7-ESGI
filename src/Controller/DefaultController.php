<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(EntityManagerInterface $entityManager): Response
    {

        $message = 'Bienvenue !';
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('index.html.twig', [
            'message' => $message,
            'events' => $events,
        ]);
    }

    #[\Symfony\Component\Routing\Annotation\Route('/admin_dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        $participants = $entityManager->getRepository(Participant::class)->findAll();

        return $this->render('admin/admin_dashboard.html.twig', [
            'participants' => $participants,
            'events' => $events,
        ]);

    }
}
