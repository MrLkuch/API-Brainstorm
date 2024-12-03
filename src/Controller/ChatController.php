<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Chatroom;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // redirigez vers la page de connexion si non connecté
        }

        // Récupérez les chatrooms associées à l'utilisateur
        $userChatrooms = $user->getUserChatrooms();

        return $this->render('chat/index.html.twig', [
            'userChatrooms' => $userChatrooms,
        ]);
    }

    #[Route('/chat/{id}', name: 'app_chat_room')]
    public function chatroom(Chatroom $chatroom): Response
    {
        // Vérifiez si la chatroom existe et si l'utilisateur y a accès
        $user = $this->getUser();
        if (!$user || !$chatroom->getUserChatrooms()->contains($user)) {
            throw $this->createAccessDeniedException('You do not have access to this chatroom.');
        }

        return $this->render('chat/chatroom.html.twig', [
            'chatroom' => $chatroom,
        ]);
    }

    #[Route('/chat/{id}/send', name: 'app_chat_send', methods: ['POST'])]
    public function sendMessage(Request $request, Chatroom $chatroom, EntityManagerInterface $entityManager): Response
    {
        $content = $request->request->get('content');
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // redirigez vers la page de connexion si non connecté
        }

        // Créez un nouveau message et associez-le à l'utilisateur et à la chatroom
        if (!empty($content)) {
            $message = new Message();
            $message->setContent($content);
            $message->setUser($user);
            $message->setChatroom($chatroom);
            $message->setSentAt(new \DateTime());

            // Persistez le message dans la base de données
            $entityManager->persist($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_room', ['id' => $chatroom->getId()]);
    }
}