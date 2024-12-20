<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ServiceController extends AbstractController
{
    #[Route('/service/new', name: 'new_service')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            $this->addFlash('success', 'Le service a été créé avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('service/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/service/{id}', name: 'edit_service')]
    public function edit(Request $request,EntityManagerInterface $entityManager, $id): Response
    {
        $service = $entityManager->getRepository(Service::class)->find($id);
        $form = $this->createForm(ServiceType::class, $service);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();

            $this->addFlash('success', 'Le service a été modifié avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('service/edit.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }
}
