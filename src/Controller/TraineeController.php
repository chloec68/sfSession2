<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Trainee;
use App\Form\TraineeType;
use App\Repository\SessionRepository;
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
            $this->addFlash('success', 'Success');
            return $this->redirectToRoute('app_trainee');
        }

        return $this->render('trainee/new-update-trainee.html.twig', [
            'formNewTrainee' => $form,
            'edit' => $trainee->getId()
        ]);

    }


    #[Route('/session/{id}/trainee/{traineeId}/delete', name: 'delete_trainee-from-session')]
    public function deleteTraineeInSession(EntityManagerInterface $entityManager, SessionRepository $sessionRepository,TraineeRepository $traineeRepository, int $id, int $traineeId)
    {       
    $session = $sessionRepository->find($id);
    // $session = $entityManager->getRepository(Session::class)->find($sessionId);
    $notEnrolled = $sessionRepository->findNotEnrolled($session->getId());

    $trainee = $traineeRepository->find($traineeId);

    $session->removeTrainee($trainee);

    $entityManager->flush();

    $this->addFlash('success', 'Trainee successfully deleted from session'); 

    return $this->render('session/detail_session.html.twig', ['traineeId' => $traineeId,'session'=>$session,'notEnrolled' => $notEnrolled]);
    }   


    #[Route('/trainee/delete/{id}', name: 'delete_trainee')]
    public function deleteTrainee(Trainee $trainee, EntityManagerInterface $entityManager, )
    {   
        $entityManager->remove($trainee);
        $entityManager->flush();
        $this->addFlash('success', 'Trainee successfully deleted'); 
        return $this->redirectToRoute('app_trainee');
    }

    #[Route('/trainee/{id}', name: 'detail_trainee')]
    public function detailTrainee(Trainee $trainee): Response
    {
        return $this->render('trainee/detailTrainee.html.twig', [
            'trainee'=>$trainee
        ]);
    }

    #[Route('/add-trainee/{idTrainee}/session/{idSession}', name:'add_trainee_to_session')]
    public function addTraineeToSesion(EntityManagerInterface $entityManager, TraineeRepository $traineeRepository, SessionRepository $sessionRepository, int $idTrainee, int $idSession)
    {   
        $session = $sessionRepository->find($idSession);
        $notEnrolled = $sessionRepository->findNotEnrolled($session->getId());
        $trainee = $traineeRepository->find($idTrainee);
  
            if($session->getAvailibility() == true && $trainee->isTraineeAvailable($session) == true){
                $session->addTrainee($trainee);
                $entityManager->persist($trainee);
                $entityManager->flush();
                $this->addFlash('success', 'Trainee successfully added in session'); 
                // return $this->redirectToRoute('app_session');
                return $this->render('session/detail_session.html.twig',
                ['session'=>$session, 'trainee'=>$trainee, 'idSession'=>$idSession,'idTrainee'=>$idTrainee, 'notEnrolled' => $notEnrolled]);
            }else if($session->getAvailibility() == false){
                $this->addFlash('error', 'This session is already fully booked');
                // return $this->redirectToRoute('app_session');
                return $this->render('session/detail_session.html.twig',
                ['session'=>$session, 'trainee'=>$trainee, 'idSession'=>$idSession,'idTrainee'=>$idTrainee, 'notEnrolled' => $notEnrolled]);
            }else if($trainee->isTraineeAvailable($session) == false){
                $this->addFlash('error', 'This trainee is already registered in an ongoing session');
                // return $this->redirectToRoute('app_session');
                return $this->render('session/detail_session.html.twig',
                ['session'=>$session, 'trainee'=>$trainee, 'idSession'=>$idSession,'idTrainee'=>$idTrainee, 'notEnrolled' => $notEnrolled]);
            }else{
                $this->addFlash('error', 'Something went wrong');
                // return $this->redirectToRoute('app_session');
                return $this->render('session/detail_session.html.twig',
                ['session'=>$session, 'trainee'=>$trainee, 'idSession'=>$idSession,'idTrainee'=>$idTrainee, 'notEnrolled' => $notEnrolled]);
            }
    
        return $this->render('session/detail_session.html.twig',
        ['session'=>$session, 'trainee'=>$trainee, 'idSession'=>$idSession,'idTrainee'=>$idTrainee, 'notEnrolled' => $notEnrolled]);
    }
}



