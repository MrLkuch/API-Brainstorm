<?php

namespace App\Controller;

use App\Entity\UserChatroom;
use App\Form\UserChatroomType;
use App\Repository\ChatroomRepository;
use App\Repository\UserRepository;
use App\Repository\UserChatroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/chatroom')]
final class UserChatroomController extends AbstractController{
    #[Route(name: 'app_user_chatroom_index', methods: ['GET'])]
    public function index(UserChatroomRepository $userChatroomRepository): Response
    {
        return $this->render('user_chatroom/index.html.twig', [
            'user_chatrooms' => $userChatroomRepository->findAll(),
        ]);
    }

    #[Route('/new/{chatroomId}', name: 'app_user_chatroom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, ChatroomRepository $chatroomRepository, $chatroomId): Response
    {
        // Récupérer la chatroom par son ID
        $chatroom = $chatroomRepository->find($chatroomId);
    
        // Si la chatroom n'existe pas, rediriger avec un message d'erreur
        if (!$chatroom) {
            $this->addFlash('error', 'Chatroom introuvable.');
            return $this->redirectToRoute('app_user_chatroom_index');
        }
    
        // Récupérer l'email de l'utilisateur à ajouter via la requête (par exemple, en GET ou POST)
        $email = $request->get('email');
    
        if ($email) {
            // Trouver l'utilisateur par email
            $user = $userRepository->findOneByEmail($email);
    
            if ($user) {
                // Ajouter l'utilisateur à la chatroom
                $userChatroom = new UserChatroom();
                $userChatroom->setUser($user);
                $userChatroom->setChatroom($chatroom);
    
                $entityManager->persist($userChatroom);
                $entityManager->flush();
    
                // Rediriger vers la chatroom après l'ajout
                return $this->redirect('http://localhost:8001/chat/' . $chatroomId);
            } else {
                $this->addFlash('error', 'Utilisateur non trouvé avec cet email.');
            }
        }
    
        // Si aucun email n'est fourni, ou après l'ajout, rediriger vers la page de la chatroom
        return $this->redirect('http://localhost:8001/chat/' . $chatroomId);
    }
    
    
    

    #[Route('/{id}', name: 'app_user_chatroom_show', methods: ['GET'])]
    public function show(UserChatroom $userChatroom): Response
    {
        return $this->render('user_chatroom/show.html.twig', [
            'user_chatroom' => $userChatroom,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_chatroom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserChatroom $userChatroom, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserChatroomType::class, $userChatroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_chatroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_chatroom/edit.html.twig', [
            'user_chatroom' => $userChatroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_chatroom_delete', methods: ['POST'])]
    public function delete(Request $request, UserChatroom $userChatroom, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userChatroom->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userChatroom);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_chatroom_index', [], Response::HTTP_SEE_OTHER);
    }
}
