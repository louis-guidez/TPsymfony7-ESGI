<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticlesRepository;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(ServiceRepository $serviceRepository): Response
    {

        $services = $serviceRepository->findAll();
        $message = 'Bienvenue !';

        return $this->render('index.html.twig', [
            'message' => $message,
            'services' => $services,
        ]);
    }
}
