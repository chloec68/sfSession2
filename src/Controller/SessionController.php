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


    #[Route('/training/{id}/add-session', name: 'new_session')]
    #[Route('/session/{id}/update', name: 'update_session')]
        public function add_update_Session(int $id,TrainingRepository $trainingRepository, Request $request,EntityManagerInterface $entityManager, ?Session $session =null): Response
        {
        //1. le paramètre d'URL {id} est passé à la méthode = la valeur d'{id} est injecté dans l'argument $id
        //2. TrainingRepository => une class de Repository => permet d'agir avec la BDD pour l'entité Training : récupérer l'entité, injecter données, etc
        //3. Request => la requête HTTP envoyée par le navigateur (client) => toutes les informations envoyées par l'utilisateur
        // la méthode handleRequest() provient de l'objet Form créé par Symfony
        //4. EntityManagerInterface est fournie par Doctrine ORM (Object-Relational-Mapper) => permet de manipuler des objets PHP associés à des tables en BDD == entités 
        // dans le contexte de Doctrine ORM // POO=>objets // Doctrine ORM=>entités // 
            // Une entité == une classe PHP == une table /=>> une instance de la classe PHP == une ligne de la table 
            // une entité == un modèle qui reflète la stucture de la table en BDD
            // une entité est "mappée" à une table en BDD grâce aux @ORM 
            // les méthodes d'EntityManager : find(), findOneBy(),getRepository(),persist(),flush(),remove(),getRepository()
            // Entity Manager => outil ppal de doctrine pour gérer la persistance des entités (== l'enregistrement et la gestion de l'état d'un objet dans la base de données : ajouter, modifier, supprimer...) : lors de la création d'un nouvel objet PHP, il est enregistré en mémoire, mais n'est pas persistant (enregistré en BDD)
            //EntityManagerInterface est la définition des méthodes que tout gestionnaire d'entités doit avoir, mais c'est une interface, pas une implémentation concrète.
            //EntityManager est une implémentation concrète de EntityManagerInterface. C'est l'implémentation fournie par Doctrine, qui contient la logique réelle pour manipuler les entités et les synchroniser avec la base de données.
            //c’est EntityManager qui effectue les opérations réelles sur la base de données, mais dans le respect du "contrat" défini par l'interface EntityManagerInterface.
            if(!$session){
                $session = new Session();
                $training = $trainingRepository->find($id);
                $session->setTraining($training);
            }
    
            $form = $this->createForm(SessionType::class,$session);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                
                $training->addSession($session);
                $session = $form->getData();
                $entityManager->persist($session); // équivalent $pdo->prepare
                $entityManager->flush(); // équivalent $pdo->execute // exécution de l'enregistrement en BDD
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
    // #[Route('/session/{id}/add-program', name: 'add_program')]
    // public function addProgram(Request $request, EntityManagerInterface $entityManager, int $id, SessionRepository $sessionRepository):Response
    // {   
    //     $program = new Program();

    //     $session = $sessionRepository->find($id);
        
    //     $form = $this->createForm(ProgramType::class, $proagram);
    //     $form->handleRequest($request);

    //     if($form->isSubmitted() && $form->isValid()){
    //         $program = $form->setData();
    //         $session->addProgram($program);
    //         $program->setSession($session);

    //         $entityManager->persist($program);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('detail_program');
    //     }
    //     return $this->render('program/add_program.html.twig',[
    //         'form'=>$form
    //     ]);
    // }
}

