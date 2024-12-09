<?php

namespace App\Controller;

use App\Entity\Chatroom;
use App\Entity\UserChatroom;
use App\Form\ChatroomType;
use App\Repository\ChatroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

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
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        Security $security
    ): Response {
        $chatroom = new Chatroom();
        $form = $this->createForm(ChatroomType::class, $chatroom);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer la chatroom
            $entityManager->persist($chatroom);
            $entityManager->flush();
    
            // Associer l'utilisateur à la chatroom
            $user = $security->getUser(); // Obtenir l'utilisateur connecté
            if ($user) {
                $userChatroom = new UserChatroom();
                $userChatroom->setUser($user);
                $userChatroom->setChatroom($chatroom);
    
                $entityManager->persist($userChatroom);
                $entityManager->flush();
            }
    
            return $this->redirect('http://localhost:8000/chat');
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
        if ($this->isCsrfTokenValid('delete'.$chatroom->getId(), $request->request->get('_token'))) {
            // Supprimer les enregistrements dans la table user_chatroom
            foreach ($chatroom->getUserChatrooms() as $userChatroom) {
                $entityManager->remove($userChatroom);
            }
            foreach ($chatroom->getMessages() as $message) {
                $entityManager->remove($message);
            }
            // Supprimer la chatroom
            $entityManager->remove($chatroom);
            $entityManager->flush();
        }
    
        return $this->redirect('http://localhost:8001/chat');
    }
    
    
}