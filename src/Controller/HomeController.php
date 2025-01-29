<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/courses', name: 'app_course')]
    public function allCourses(CourseRepository $courseRepository): Response
    {
        $courses=$courseRepository->findBy([],['name'=>'ASC']);
        return $this->render('course/index.html.twig', [
            "courses" => $courses,
        ]);
    }

}
