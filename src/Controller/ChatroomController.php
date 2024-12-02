<?php

namespace App\Controller;

use App\Entity\Chatroom;
use App\Form\ChatroomType;
use App\Repository\ChatroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chatroom')]
final class ChatroomController extends AbstractController {
    #[Route(name: 'app_chatroom_index', methods: ['GET'])]
    public function index(ChatroomRepository $chatroomRepository): Response
    {
        return $this->render('chatroom/index.html.twig', [
            'chatrooms' => $chatroomRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chatroom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chatroom = new Chatroom();
        $form = $this->createForm(ChatroomType::class, $chatroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chatroom);
            $entityManager->flush();

            return $this->redirectToRoute('app_chatroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chatroom/new.html.twig', [
            'chatroom' => $chatroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chatroom_show', methods: ['GET'])]
    public function show(Chatroom $chatroom): Response
    {
        return $this->render('chatroom/show.html.twig', [
            'chatroom' => $chatroom,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chatroom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chatroom $chatroom, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatroomType::class, $chatroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de persister à nouveau, car l'entité existe déjà.
            // Juste appeler flush pour sauvegarder les changements.
            $entityManager->flush();

            return $this->redirectToRoute('app_chatroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chatroom/edit.html.twig', [
            'chatroom' => $chatroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chatroom_delete', methods: ['POST'])]
    public function delete(Request $request, Chatroom $chatroom, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chatroom->getId(), $request->request->get('_token'))) { // Correction ici pour utiliser request au lieu de getPayload()
            // Supprimer la chat room et sauvegarder les changements.
            $entityManager->remove($chatroom);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chatroom_index', [], Response::HTTP_SEE_OTHER);
    }
}