<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\JwtProvider;
use App\Entity\Chatroom;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(EntityManagerInterface $entityManager): Response
    { 
        // Vérifiez si l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user || !$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }        
    
        // Récupérez les chatrooms associées à l'utilisateur
        $userChatrooms = $user->getUserChatrooms();
    
        return $this->render('chat/index.html.twig', [
            'chatrooms' => $userChatrooms, // Assurez-vous que la variable est correctement nommée ici
        ]);
    }

    #[Route('/chat/{id}', name: 'app_chat_room')]
    public function chatroom(Chatroom $chatroom): Response
    {
        // Vérifiez si l'utilisateur est connecté
        $user = $this->getUser();
        
        if (!$user || !$user instanceof User) {
            return $this->redirectToRoute('app_login'); // Redirigez vers la page de connexion si non connecté
        }
    
       // Vérifiez si l'utilisateur a accès à cette chatroom via UserChatroom
       $hasAccess = false;
       foreach ($user->getUserChatrooms() as $userChatroom) {
           if ($userChatroom->getChatroom() === $chatroom) {
               $hasAccess = true;
               break;
           }
       }
    
       if (!$hasAccess) {
           throw $this->createAccessDeniedException('You do not have access to this chatroom.');
       }
    
       return $this->render('chat/chatroom.html.twig', [
           'chatroom' => $chatroom,
       ]);
    }

    #[Route('/chat/{id}/send', name: 'app_chat_send', methods: ['POST'])]
public function sendMessage(
    JwtProvider $jwtProvider,
    Request $request,
    Chatroom $chatroom,
    EntityManagerInterface $entityManager,
    HubInterface $hub
): Response {
    $content = trim($request->request->get('content'));
    $user = $this->getUser();
    // $jwt = $jwtProvider->createJwt();

    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    if (!empty($content)) {
        // Sauvegarde du message dans la base de données
        $message = new Message();
        $message->setContent($content);
        $message->setUser($user);
        $message->setChatroom($chatroom);
        $message->setSentAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        $chatroomId = $chatroom->getId();

        // // Publication via Mercure
        // $update = new Update(
        //     // Le topic utilise l'URL de la chatroom (port 8001)
        //     "http://localhost:8001/chat/{$chatroomId}",
        //     json_encode([
        //         'content' => $message->getContent(),
        //         'sender' => $message->getUser()->getUsername(),
        //         'chatroomId' => $chatroomId
        //     ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        // );

        // try {
        //     $hub->publish($update);
        // } catch (\Exception $e) {
        //     error_log("Erreur Mercure : " . $e->getMessage());
        // }
    }

    return $this->redirectToRoute('app_chat_room', ['id' => $chatroom->getId()]);
}


}