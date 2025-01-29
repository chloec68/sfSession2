<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Session;
use App\Entity\Training;
use App\Form\SessionType;
use App\Repository\SessionRepository;
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


    #[Route('/session/new', name: 'new_session')]
    #[Route('/session/{id}/update', name: 'update_session')]
        public function add_update_Session(Request $request,EntityManagerInterface $entityManager, ?Session $session =null): Response
        {
            if(!$session){
                $session = new Session();
            }

            $form = $this->createForm(SessionType::class,$session);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $session = $form->getDate();
                $sessionManager >persist($ession); // équivalent $pdo->prepare
                $sessionManager->flush(); // équivalent $pdo->execute
                return $this->redirectToRoute('app_session');
            }
            return $this->render('session/new_session.html.twig', [
                'formNewSession'=>$form,
                'edit' => $session->getId() // si l'entreprise est déjà créée, un id est renvoyé (renvoie bool:true) / sinon bool:false
            ]);
        }

    #[Route('/session/{id}', name: 'detail_session')]
    public function detailSession(Session $session): Response
    {
        return $this->render('session/detail_session.html.twig', [
            'session' => $session
        ]);
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

