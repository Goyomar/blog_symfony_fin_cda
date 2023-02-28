<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $ar): Response
    {
        $latestArticles = $ar->findBy([], ["created_at" => 'DESC'], 10);
        return $this->render('home/index.html.twig', [
            'latestArticles' => $latestArticles,
        ]);
    }
}
