<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_category')]
    public function index(Category $category): Response
    {
        return $this->render('category/index.html.twig', [
            'articles' => array_reverse($category->getArticles()->toArray()),
        ]);
    }

    public function allCategories(CategoryRepository $cr)
    {
        return $this->render('inc/sideMenu.html.twig', [
            'categories' => $cr->findAll()
        ]);
    }
}
