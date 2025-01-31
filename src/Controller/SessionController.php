<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Session;
use App\Entity\Training;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Repository\TraineeRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository ): Response
    {   
        $totalSessions = $sessionRepository->countSessions();
        $sessions = $sessionRepository->findBy([],['name'=>'ASC']);     

        return $this->render('session/index.html.twig', [
            'totalSessions' => $totalSessions,
            'sessions' => $sessions
        ]);
    }

    #[Route('/training', name: 'app_training')]
    public function training(TrainingRepository $trainingRepository): Response
    {
        $trainings = $trainingRepository->findBy([],['name'=>'ASC']);
        return $this->render('training/index.html.twig', [
            'trainings' => $trainings,
        ]);
    }


    #[Route('/training/{id}/add', name: 'add_session')]
    public function add_session(int $id,TrainingRepository $trainingRepository, TraineeRepository $traineeRepository, Request $request, EntityManagerInterface $entityManager):Response
    {
	$training = $trainingRepository->find($id);

	$session = new Session();

    $trainees = $session->getTrainees();

	$form=$this->createForm(SessionType::class,$session);

	$form->handleRequest($request);

	if($form->isSubmitted() && $form->isValid()){
		$session = $form->getData();
		$session->setTraining($training);

		if(count($session->getTrainees()) !== null){
			foreach($trainees as $trainee){
				$session->addTrainee($trainee);
                $trainee->addSession($session);
			}
		$entityManager->persist($session);
		$entityManager->flush();
		return $this->redirectToRoute('app_session');
        }
	}
	return $this->render('session/new_update_session.html.twig', [
		'formNewSession' => $form,	
	]);
}

    #[Route('/session/{id}/update', name: 'update_session')]
    public function updateSession(Session $session, int $id, Request $request, EntityManagerInterface $entityManager):Response
    {
        $form = $this->createForm(SessionType::class,$session);
        $form->handleRequest($request);
        $nbPlaces = $session->getNbPlaces();
        

        if($form->isSubmitted() && $form->isValid()){
            $session = $form->getData();
            $trainees = $session->getTrainees();
            $nbTrainees = count($trainees);

            if($nbPlaces < $nbTrainees){
                return $this->redirectToRoute('app_session');
            }else{
                foreach($trainees as $trainee){
                    $session->addTrainee($trainee);
                    $trainee->addSession($session);
                }
            }
            $entityManager->persist($session);
            $entityManager->flush();
            return $this->redirectToRoute('app_session');
        }
        return $this->render('session/new_update_session.html.twig', [
            'formNewSession'=>$form,
   
        ]);
    }



    #[Route('/session/{id}', name: 'detail_session')]
    public function detailSession(Session $session, SessionRepository $sessionRepository): Response
    {
        $notEnrolled = $sessionRepository->findNotEnrolled($session->getId());

        return $this->render('session/detail_session.html.twig', [
            'session' => $session,
            'notEnrolled' => $notEnrolled
        ]);
    }


    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function deleteSession(Session $session, EntityManagerInterface $entityManager): Response
    {
        $trainees = $session->getTrainees();
        $programs = $session->getPrograms();

        foreach ($trainees as $trainee) {
            $session->removeTrainee($trainee);
        }

        foreach ($programs as $program){
            $session->removeProgram($program);
        }

        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }


    #[Route('/training/{id}', name: 'detail_training')]
    public function trainingDetails(Training $training): Response
    {
        return $this->render('training/detailTraining.html.twig',[
            'training' => $training
        ]);
    }

    #[Route('/session/program/{id}', name: 'detail_program')]
    public function programDetails(Session $session): Response
    {
        return $this->render('program/detailProgram.html.twig',[
            'session' => $session
        ]);
    }

}

