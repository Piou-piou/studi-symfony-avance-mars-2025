<?php

namespace App\Controller;

use App\Entity\StockArticle;
use App\Form\StockArticleType;
use App\Repository\StockArticleRepository;
use App\Service\FormHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/article/stock', name: 'stock_')]
final class StockArticleController extends AbstractController
{
    #[Route('/', name: 'article_index', methods: ['GET'])]
    public function index(RouterInterface $router, StockArticleRepository $stockArticleRepository): Response
    {
        return $this->render('article_stock/index.html.twig', [
            'article_stocks' => $stockArticleRepository->findAll(),
            'createLink' => $router->generate('stock_article_new'),
        ]);
    }

    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(FormHandler $formHandler): Response
    {
        return $formHandler->handleForm(StockArticleType::class, new StockArticle(), 'Ajouter un article en stock', $this->generateUrl('stock_article_index'));
    }

    #[Route('/{id}/edit', name: 'article_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(FormHandler $formHandler, StockArticle $stock): Response
    {
        return $formHandler->handleForm(StockArticleType::class, $stock, 'Modifier ce stock', $this->generateUrl('stock_article_index'));
    }

    public function delete(EntityManagerInterface $em, StockArticle $stockArticle): Response
    {
        $em->remove($stockArticle);
        $em->flush();

        return $this->redirectToRoute('stock_article_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/increase', name: 'article_increase', requirements: ['id' => '\d+'], defaults: ['increase' => true], methods: ['GET'])]
    #[Route('/{id}/decrease', name: 'article_decrease', requirements: ['id' => '\d+'], defaults: ['increase' => false], methods: ['GET'])]
    public function increaseStock(Request $request, EntityManagerInterface $em, StockArticle $stockArticle, bool $increase): JsonResponse
    {
        $newQuantity = $stockArticle->getQuantity();
        if (true === $increase) {
            $newQuantity += 1;
        } else {
          $newQuantity -= 1;
        }

        $stockArticle->setQuantity($newQuantity);
        $em->persist($stockArticle);
        $em->flush();

        return new JsonResponse([
            'stock' => $stockArticle->getQuantity(),
        ], Response::HTTP_OK);
    }
}
