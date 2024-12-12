<?php

namespace App\Controller;

use App\Entity\Proof;
use App\Form\ProofType;
use App\Repository\ProofRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/proof')]
final class ProofController extends AbstractController
{
    #[Route(name: 'app_proof_index', methods: ['GET'])]
    public function index(ProofRepository $proofRepository): Response
    {
        return $this->render('proof/index.html.twig', [
            'proofs' => $proofRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_proof_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $proof = new Proof();
        $form = $this->createForm(ProofType::class, $proof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($proof);
            $entityManager->flush();

            return $this->redirectToRoute('app_proof_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('proof/new.html.twig', [
            'proof' => $proof,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_proof_show', methods: ['GET'])]
    public function show(Proof $proof): Response
    {
        return $this->render('proof/show.html.twig', [
            'proof' => $proof,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_proof_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Proof $proof, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProofType::class, $proof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_proof_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('proof/edit.html.twig', [
            'proof' => $proof,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_proof_delete', methods: ['POST'])]
    public function delete(Request $request, Proof $proof, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$proof->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($proof);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_proof_index', [], Response::HTTP_SEE_OTHER);
    }
}
