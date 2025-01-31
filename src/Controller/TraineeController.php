<?php

namespace App\Controller;

use App\Entity\Trainee;
use App\Form\TraineeType;
use App\Repository\TraineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/trainee/add', name: 'add_trainee')]
    #[Route('/trainee/{id}/update', name: 'update_trainee')]
    public function AddUpdateTrainee(Request $request, EntityManagerInterface $entityManager, ?Trainee $trainee = null): Response
    {
        if(!$trainee){
            $trainee = new Trainee();
        }

        $form = $this->createForm(TraineeType::class, $trainee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $trainee = $form->getData();
            $entityManager->persist($trainee);
            $entityManager->flush();
            return $this->redirectToRoute('app_trainee');
        }

        return $this->render('trainee/new-update-trainee.html.twig', [
            'formNewTrainee' => $form,
            'edit' => $trainee->getId()
        ]);

    }

    #[Route('/trainee/{id}/delete', name: 'delete_trainee-in-session')]
    public function deleteTraineeInSession(Trainee $trainee, EntityManagerInterface $entityManager, int $id)
    {
        $entityManager->remove($trainee);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }

    #[Route('/trainee/delete/{id}', name: 'delete_trainee')]
    public function deleteTrainee(Trainee $trainee, EntityManagerInterface $entityManager, )
    {   
        $entityManager->remove($trainee);
        $entityManager->flush();
        return $this->redirectToRoute('app_trainee');
    }

    #[Route('/trainee/{id}', name: 'detail_trainee')]
    public function detailTrainee(Trainee $trainee): Response
    {
        return $this->render('trainee/detailTrainee.html.twig', [
            'trainee'=>$trainee
        ]);
    }

}

