<?php

namespace App\Controller;

use App\Entity\Trainee;
use App\Repository\TraineeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TraineeController extends AbstractController
{   
    #[Route('/trainee', name: 'app_trainee')]
    public function index(TraineeRepository $traineeRepository): Response
    {
        $trainees=$traineeRepository->findBy([],['firstName'=>'ASC']);

        $totalTrainees = $traineeRepository->countTrainees();

        return $this->render('trainee/index.html.twig', [
            'controller_name' => 'TraineeController',
            'trainees'=>$trainees,
            'totalTrainees'=>$totalTrainees
        ]);
    }

    #[Route('/trainee/{id}', name: 'detail_trainee')]
    public function detailTrainee(Trainee $trainee): Response
    {
        return $this->render('trainee/detailTrainee.html.twig', [
            'trainee'=>$trainee
        ]);
    }
}

