<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\Chatroom;
use App\Service\GroqService;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    #[Route('/api/synthesize/{chatroomId}', name: 'api_synthesize', methods: ['POST'])]
    public function synthesize(Request $request, int $chatroomId, EntityManagerInterface $entityManager, MessageRepository $messageRepository): JsonResponse
    {
        // Récupérer les messages de la chatroom
        $messages = $messageRepository->findBy(['chatroom' => $chatroomId], ['sendAt' => 'ASC']);
        
        // Formater les messages pour l'API Groq
        $formattedMessages = array_map(function($message) {
            return $message->getUser()->getEmail() . ": " . $message->getContent();
        }, $messages);
        
        // Envoyer la requête à l'API Groq avec le formatage requis
        try {
            $response = $this->groqService->sendRequest($formattedMessages);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    
        // Créer un nouveau message avec la synthèse
        $synthesisMessage = new Message();
        $synthesisMessage->setContent($response['output'] ?? 'Synthèse indisponible') // Exemple : Adaptation au format de la réponse
                         ->setChatroom($entityManager->getReference(Chatroom::class, $chatroomId))
                         ->setUser($entityManager->getReference(User::class, 666)) // Utilisateur système
                         ->setSentAt(new \DateTime());
        
        // Persister le nouveau message
        $entityManager->persist($synthesisMessage);
        $entityManager->flush();
        
        return $this->json(['message' => 'Synthèse ajoutée avec succès', 'synthesis' => $response]);
    }    

    // #[Route('/api/generate-idea', name: 'api_generate_idea', methods: ['POST'])]
    // public function generateIdea(Request $request): JsonResponse
    // {
    //     $messages = json_decode($request->getContent(), true)['messages'];
    //     $prompt = "Générez une idée liée à cette discussion : " . implode("\n", $messages);
    //     $response = $this->groqService->sendRequest($prompt);
    //     return $this->json($response);
    // }

    // #[Route('/api/critique', name: 'api_critique', methods: ['POST'])]
    // public function critique(Request $request): JsonResponse
    // {
    //     $messages = json_decode($request->getContent(), true)['messages'];
    //     $prompt = "Critiquez le contenu de cette discussion : " . implode("\n", $messages);
    //     $response = $this->groqService->sendRequest($prompt);
    //     return $this->json($response);
    // }

    // #[Route('/api/custom-prompt', name: 'api_custom_prompt', methods: ['POST'])]
    // public function customPrompt(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $messages = $data['messages'];
    //     $customPrompt = $data['customPrompt'];
    //     $prompt = $customPrompt . " : " . implode("\n", $messages);
    //     $response = $this->groqService->sendRequest($prompt);
    //     return $this->json($response);
    // }
}
