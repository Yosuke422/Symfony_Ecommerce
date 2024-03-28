<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UpdateCompteType;

class CompteController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/compte', name: 'app_compte')]
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('compte/{id}/edit', name: 'app_compte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UpdateCompteType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $user,
            'form' => $form,
        ]);
    }
}
