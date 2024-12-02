<?php
namespace App\Controller;

use App\Repository\ChatroomRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chat')]
class ChatController extends AbstractController
{
    #[Route('/', name: 'chat_home')]
    public function index(
        ChatroomRepository $chatroomRepository,
        MessageRepository $messageRepository,
        Request $request
    ): Response {
        $user = $this->getUser(); // Vérifie si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder aux chatrooms.');
        }
    
        // Récupère les chatrooms auxquelles l'utilisateur est inscrit
        $chatrooms = $chatroomRepository->findByUser($user);
    
        // Gère la chatroom active (par défaut, la première)
        $chatroomId = $request->query->get('chatroom', $chatrooms[0]->getId() ?? null);
        $activeChatroom = $chatroomRepository->find($chatroomId);
    
        // Récupère les messages de la chatroom active
        $messages = $activeChatroom 
            ? $messageRepository->findBy(['_chatroom' => $activeChatroom], ['sentAt' => 'ASC']) 
            : [];
    
        // Transmet les données à la vue Twig
        return $this->render('chat/index.html.twig', [
            'chatrooms' => $chatrooms,
            'activeChatroom' => $activeChatroom,
            'messages' => $messages,
            'user' => $user,
        ]);
    }
}