<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Recruiter;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Recruiter) {
            throw $this->createAccessDeniedException('Vous devez être recruteur pour ajouter un post.');
        }

        $query = $em->createQuery("SELECT p 
                                  FROM App\Entity\Post p
                                  JOIN p.recruiter r
                                  WHERE r.id = :id")->setParameter("id",$user->getId());
        $tab_res = $query->getResult();

        return $this->render('post/index.html.twig', [
            'posts' =>  $tab_res,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
       // Vérifie si l'utilisateur connecté est un Recruiter
       $user = $this->getUser();

       if (!$user instanceof Recruiter) {
           throw $this->createAccessDeniedException('Vous devez être recruteur pour ajouter un post.');
       }

       $post = new Post();
       $post->setRecruiter($user); // Liaison directe entre le Recruiter et le Post

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post,EntityManagerInterface $em,$id): Response
    {
        $query = $em->createQuery("SELECT c 
                                  FROM App\Entity\Criteria c
                                  JOIN c.post p
                                  WHERE p.id = :id")->setParameter("id",$id);
        $tab_res = $query->getResult();
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'criterias' => $tab_res
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
