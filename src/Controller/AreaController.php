<?php

namespace App\Controller;

use App\Entity\Area;
use App\Form\AreaType;
use App\Repository\AreaRepository;
use App\Service\FormHandler;
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
    public function new(FormHandler $formHandler): Response
    {
        return $formHandler->handleForm(AreaType::class, new Area(), 'Ajouter un emplacement', $this->generateUrl('area_index'));
    }

    #[Route('/{id}/edit', name: 'area_edit', methods: ['GET', 'POST'])]
    public function edit(FormHandler $formHandler, Area $area): Response
    {
        return $formHandler->handleForm(AreaType::class, $area, 'Modifier un emplacement', $this->generateUrl('area_index'));
    }

    #[Route('/{id}', name: 'area_show', methods: ['GET'])]
    public function show(RouterInterface $router, Area $area): Response
    {
        return $this->render('area/show.html.twig', [
            'entity' => $area,
            'deleteLink' => $router->generate('area_delete', ['id' => $area->getId()]),
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
