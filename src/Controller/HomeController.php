<?php

namespace App\Controller;
use App\Form\UserAvatarFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{   

    #[Route('/',name:'root')]
    public function redirectToHome(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    } 

    #[Route('/profile', 'app_profile')]
    public function profile(EntityManagerInterface $em, Request $request) : Response
    {   

        $user = $this->getUser();

        $form = $this->createForm(UserAvatarFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['avatar']->getData();

            if ($uploadedFile) {
                // Génére un nom unique pour le fichier
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $uploadedFile->guessExtension();
                $newFilename = "user" . $user->getId() . "-" . uniqid() ."." . $extension;
                // Si le nom de fichier dépasse 50 caractères, le tronquer
                if (strlen($newFilename) > 50) {
                    $newFilename = substr($newFilename, 0, 50 - strlen($extension)) . $extension;
                }
    
                // Déplace le fichier vers le dossier de destination
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads/avatars';
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );

                $path = $destination ."/". $newFilename ;

                $relativePath = str_replace($this->getParameter('kernel.project_dir') . '/public', '', $path);

                $user->setAvatar($relativePath);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success','successfully uploaded');
                return $this->redirectToRoute('app_profile');
            }
        }
        return $this->render('home/profile.html.twig',[
            'form' => $form
        ]);
    }
}