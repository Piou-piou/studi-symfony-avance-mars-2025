<?php

namespace App\Controller;

use App\Entity\Area;
use App\Form\AreaType;
use App\Repository\AreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/area')]
final class AreaController extends AbstractController
{
    #[Route(name: 'area_index', methods: ['GET'])]
    public function index(AreaRepository $areaRepository): Response
    {
        return $this->render('area/index.html.twig', [
            'areas' => $areaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'area_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RouterInterface $router, EntityManagerInterface $entityManager): Response
    {
        $area = new Area();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($area);
            $entityManager->flush();

            return $this->redirectToRoute('area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud/edit.html.twig', [
            'title' => 'Ajouter un emplacement',
            'backToListLink' => $router->generate('area_index'),
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'area_show', methods: ['GET'])]
    public function show(RouterInterface $router, Area $area): Response
    {
        return $this->render('area/show.html.twig', [
            'entity' => $area,
            'deleteLink' => $router->generate('area_delete', ['id' => $area->getId()]),
        ]);
    }

    #[Route('/{id}/edit', name: 'area_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RouterInterface $router, Area $area, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud/edit.html.twig', [
            'title' => 'Modifier un emplacement',
            'backToListLink' => $router->generate('area_index'),
            'area' => $area,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'area_delete', methods: ['POST'])]
    public function delete(Request $request, Area $area, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$area->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($area);
            $entityManager->flush();
        }

        return $this->redirectToRoute('area_index', [], Response::HTTP_SEE_OTHER);
    }
}
