<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(Request $request, ArticleRepository $ar, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $titre = $form->get('title')->getData();
            $category = $form->get('category')->getData();
            $author = $form->get('author')->getData();

            // POUR L'HISTOIRE J'AVAIS PAS ENVIE DE FAIRE DE DQL
//            if($titre === null){ $titre = "";}
//            if($category === null){ $category = "";}else{$category = $category->getId();}
//            if($author === null){ $author = "";}else{$author = $author->getId();}
//            $articles = $ar->findBy(["title" => $titre, "category" => $category, "author" => $author], ["created_at" => "DESC"]);
            $articles = $ar->findSearch($titre, $category, $author);
        } else {
            $articles = $ar->findBy([], ["created_at" => 'DESC']);
        }
        return $this->render('article/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $paginator->paginate($articles,$request->query->getInt('page',1),10),
        ]);
    }

    #[Route('/article/{slug}', name: 'show_article')]
    public function show(Article $article, Request $request, ManagerRegistry $doctrine): Response
    {
        if($this->getUser()){
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $comment->setIsActive(true)
                    ->setCreatedAt(new \Datetime('now'))
                    ->setAuthor($this->getUser())
                    ->setArticle($article)
                ;
                $doctrine->getManager()->persist($comment);
                $doctrine->getManager()->flush();
                return $this->redirectToRoute('show_article');
            }
            return $this->render('article/show.html.twig', [
                'article' => $article,
                'form' => $form->createView()
            ]);
        }


        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }
}
