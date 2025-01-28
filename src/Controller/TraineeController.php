<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TraineeController extends AbstractController
{
    #[Route('/trainee', name: 'app_trainee')]
    public function index(): Response
    {
        return $this->render('trainee/index.html.twig', [
            'controller_name' => 'TraineeController',
        ]);
    }
}
