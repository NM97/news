<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/', requirements: ['_locale' => 'en|pl'])]

class ArticleController extends AbstractController
{
    #[Route('/{_locale}', methods: ['GET'], name: 'articles.index')]
    public function index(Request $request, ManagerRegistry $doctrine, string $_locale = 'en'): Response
    {
        $articles = $doctrine->getRepository(Article::class)->findAllArticles($request->query->getInt('page', 1));
        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/{_locale}/article/new', methods: ['GET', 'POST'], name: 'articles.new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $article = new Article();
        $article->setTitle('Write a article');
        $article->setText('Article text');
        $article->addUser($this->getUser());
        $article->setCreatedAt(new \DateTimeImmutable('now'));
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('articles.index');
        }
        return $this->render('article/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{_locale}/article/{id}', methods: ['GET'], name: 'articles.show')]
    public function show(Article $article, EntityManagerInterface $entityManager): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }


    #[Route('/{_locale}/article/{id}/edit', methods: ['GET', 'POST'], name: 'articles.edit')]
    public function edit(Article $article, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('POST_EDIT', $article);
        $article->setUpdatedAt(new \DateTimeImmutable('now'));
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('articles.index');
        }
        return $this->render('article/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{_locale}/article/{id}/delete', methods: ['POST'], name: 'articles.delete')]
    public function delete(Article $article, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('POST_DELETE', $article);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('articles.index');
    }
}
