<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FormHandler
{
    private Request $request;

    public function __construct(
        private FormFactoryInterface $form,
        private Environment $twig,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function handleForm(string $formType, object $entity, string $title, string $backToListLink): Response
    {
        $form = $this->form->create($formType, $entity);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return new RedirectResponse($backToListLink);
        }

        return new Response($this->twig->render('crud/edit.html.twig', [
            'title' => $title,
            'backToListLink' => $backToListLink,
            'entity' => $entity,
            'form' => $form->createView(),
        ]));
    }
}