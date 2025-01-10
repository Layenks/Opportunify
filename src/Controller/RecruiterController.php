<?php

namespace App\Controller;

use App\Entity\Recruiter;
use App\Form\RecruiterType;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/recruiter')]
final class RecruiterController extends AbstractController
{
    #[Route(name: 'app_recruiter_index', methods: ['GET'])]
    public function index(RecruiterRepository $recruiterRepository): Response
    {
        return $this->render('recruiter/index.html.twig', [
            'recruiters' => $recruiterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recruiter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher):Response
    {
        
        $recruiter = new Recruiter();
        $recruiter->setRoles(["ROLE_RECRUITER"]);
        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Hacher le mot de passe avant de persister
            $plainPassword = $recruiter->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($recruiter, $plainPassword);
            $recruiter->setPassword($hashedPassword);

            $entityManager->persist($recruiter);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recruiter/new.html.twig', [
            'recruiter' => $recruiter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recruiter_show', methods: ['GET'])]
    public function show(Recruiter $recruiter): Response
    {
        return $this->render('recruiter/show.html.twig', [
            'recruiter' => $recruiter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recruiter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recruiter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recruiter/edit.html.twig', [
            'recruiter' => $recruiter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recruiter_delete', methods: ['POST'])]
    public function delete(Request $request, Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recruiter->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($recruiter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recruiter_index', [], Response::HTTP_SEE_OTHER);
    }
}
