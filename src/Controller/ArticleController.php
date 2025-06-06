<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\FormHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/article')]
final class ArticleController extends AbstractController
{
    #[Route(name: 'article_index', methods: ['GET'])]
    public function index(RouterInterface $router, ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'createLink' => $router->generate('article_new'),
        ]);
    }

    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(FormHandler $formHandler): Response
    {
        return $formHandler->handleForm(ArticleType::class, new Article(), 'Ajouter un article', $this->generateUrl('article_index'));
    }

    #[Route('/{id}/edit', name: 'article_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(FormHandler $formHandler, Article $article): Response
    {
        return $formHandler->handleForm(ArticleType::class, $article, 'Modifier un article', $this->generateUrl('article_index'));
    }

    #[Route('/{id}', name: 'article_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(RouterInterface $router, Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'entity' => $article,
            'deleteLink' => $router->generate('article_delete', ['id' => $article->getId()]),
        ]);
    }

    #[Route('/{id}/delete', name: 'article_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, Article $article): Response
    {
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }
}
