<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Repository\ResumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/resume')]
final class ResumeController extends AbstractController
{
    #[Route(name: 'app_resume_index', methods: ['GET'])]
    public function index(ResumeRepository $resumeRepository): Response
    {
        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_resume_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resume);
            $entityManager->flush();

            return $this->redirectToRoute('app_resume_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resume/new.html.twig', [
            'resume' => $resume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resume_show', methods: ['GET'])]
    public function show(Resume $resume): Response
    {
        return $this->render('resume/show.html.twig', [
            'resume' => $resume,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_resume_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resume $resume, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_resume_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resume/edit.html.twig', [
            'resume' => $resume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resume_delete', methods: ['POST'])]
    public function delete(Request $request, Resume $resume, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resume->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($resume);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_resume_index', [], Response::HTTP_SEE_OTHER);
    }
}
