<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CourseRepository;
use App\Form\AddCategoryToCourseType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\form;

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

        foreach($courses as $course){
            $category = $course->getCategory(); 

            if(!$category){
                $course->setCategory(null);
            }
        } 
            return $this->render('course/index.html.twig', [
                "courses" => $courses,
            ]);
    }


    #[Route('/categories', name: "app_category")]
    public function allCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([],['name'=>'ASC']);
        return $this->render('category/index.html.twig', [
            "categories" => $categories
        ]);
    }


    #[Route('/category/add', name: "add_category")]
    #[Route('/category/{id}/update', name: 'update_category')]
    public function addCategory(Request $request, CategoryRepository $categoryRepository,EntityManagerInterface $entityManager, ?Category $category = null): Response
    {
        if(!$category){
            $category = new Category();
        }
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $category = $form->getData();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('app_category');
        }
        return $this->render('category/new-update-category.html.twig', [
            'formNewCategory' => $form,
            'edit' => $category->getId()
        ]);
    }

    #[Route('/category/{id}/delete', name: 'delete_category')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager)
    {   
        $courses = $category->getCourses();
        foreach($courses as $course){
            $category->removeCourse($course);
            $entityManager->remove($course);
        }
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_category');
    }

}