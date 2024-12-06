<?php

namespace App\Controller;

use App\Service\GroqService;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Chatroom;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Psr\Log\LoggerInterface;

class ApiController extends AbstractController
{
    private $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }


    #[Route('/api/synthesize/{chatroomId}', name: 'api_synthesize', methods: ["POST"])]
public function synthesize(Request $request, int $chatroomId, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
{
    try {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['messages']) || !is_array($content['messages'])) {
            throw new BadRequestHttpException('Invalid request format: messages array is required');
        }

        $messages = $content['messages'];
        $prompt = "Synthétisez la discussion suivante : \n" . implode("\n", $messages);
        $response = $this->groqService->sendRequest($prompt);

        if (!isset($response['choices'][0]['message']['content'])) {
            throw new \RuntimeException('Unexpected response format from Groq service');
        }

        $synthesisContent = $response['choices'][0]['message']['content'];

        $chatroom = $entityManager->getReference(Chatroom::class, $chatroomId);
        $user = $entityManager->getReference(User::class, 666);

        $message = new Message();
        $message->setContent($synthesisContent)
                ->setChatroom($chatroom)
                ->setUser($user)
                ->setSentAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'La synthèse a été générée et enregistrée avec succès.',
            'synthesis' => $synthesisContent
        ], JsonResponse::HTTP_OK);

    } catch (BadRequestHttpException $e) {
        $logger->warning('Bad request in synthesize: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], JsonResponse::HTTP_BAD_REQUEST);

    } catch (\Exception $e) {
        $logger->error('Error in synthesize: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => 'Une erreur interne s\'est produite lors de la synthèse.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}


#[Route('/api/generate-idea/{chatroomId}', name: 'api_generate_idea', methods: ['POST'])]
public function generateIdea(Request $request, int $chatroomId, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
{
    try {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['messages']) || !is_array($content['messages'])) {
            throw new BadRequestHttpException('Invalid request format: messages array is required');
        }

        $messages = $content['messages'];
        $prompt = "Générez une idée liée à cette discussion : \n" . implode("\n", $messages);
        $response = $this->groqService->sendRequest($prompt);

        if (!isset($response['choices'][0]['message']['content'])) {
            throw new \RuntimeException('Unexpected response format from Groq service');
        }

        $ideaContent = $response['choices'][0]['message']['content'];

        $chatroom = $entityManager->getReference(Chatroom::class, $chatroomId);
        $user = $entityManager->getReference(User::class, 666);

        $message = new Message();
        $message->setContent($ideaContent)
                ->setChatroom($chatroom)
                ->setUser($user)
                ->setSentAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Une nouvelle idée a été générée et enregistrée avec succès.',
            'idea' => $ideaContent
        ], JsonResponse::HTTP_OK);

    } catch (BadRequestHttpException $e) {
        $logger->warning('Bad request in generateIdea: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], JsonResponse::HTTP_BAD_REQUEST);

    } catch (\Exception $e) {
        $logger->error('Error in generateIdea: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => 'Une erreur interne s\'est produite lors de la génération de l\'idée.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}


#[Route('/api/critique/{chatroomId}', name: 'api_critique', methods: ['POST'])]
public function critique(Request $request, int $chatroomId, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
{
    try {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['messages']) || !is_array($content['messages'])) {
            throw new BadRequestHttpException('Invalid request format: messages array is required');
        }

        $messages = $content['messages'];
        $prompt = "Critique le contenu de cette discussion : \n" . implode("\n", $messages);
        $response = $this->groqService->sendRequest($prompt);

        if (!isset($response['choices'][0]['message']['content'])) {
            throw new \RuntimeException('Unexpected response format from Groq service');
        }

        $critiqueContent = $response['choices'][0]['message']['content'];

        $chatroom = $entityManager->getReference(Chatroom::class, $chatroomId);
        $user = $entityManager->getReference(User::class, 666);

        $message = new Message();
        $message->setContent($critiqueContent)
                ->setChatroom($chatroom)
                ->setUser($user)
                ->setSentAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Une critique de la discussion a été générée et enregistrée avec succès.',
            'critique' => $critiqueContent
        ], JsonResponse::HTTP_OK);

    } catch (BadRequestHttpException $e) {
        $logger->warning('Bad request in critique: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], JsonResponse::HTTP_BAD_REQUEST);

    } catch (\Exception $e) {
        $logger->error('Error in critique: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => 'Une erreur interne s\'est produite lors de la génération de la critique.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

#[Route('/api/custom-prompt/{chatroomId}', name: 'api_custom_prompt', methods: ['POST'])]
public function customPrompt(Request $request, int $chatroomId, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
{
    try {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['messages']) || !is_array($content['messages']) || !isset($content['customPrompt'])) {
            throw new BadRequestHttpException('Invalid request format: messages array and customPrompt are required');
        }

        $messages = $content['messages'];
        $customPrompt = $content['customPrompt'];
        $prompt = $customPrompt . "\n" . implode("\n", $messages);
        $response = $this->groqService->sendRequest($prompt);

        if (!isset($response['choices'][0]['message']['content'])) {
            throw new \RuntimeException('Unexpected response format from Groq service');
        }

        $customResponseContent = $response['choices'][0]['message']['content'];

        $chatroom = $entityManager->getReference(Chatroom::class, $chatroomId);
        $user = $entityManager->getReference(User::class, 666);

        $message = new Message();
        $message->setContent($customResponseContent)
                ->setChatroom($chatroom)
                ->setUser($user)
                ->setSentAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Une réponse personnalisée a été générée et enregistrée avec succès.',
            'customResponse' => $customResponseContent
        ], JsonResponse::HTTP_OK);

    } catch (BadRequestHttpException $e) {
        $logger->warning('Bad request in customPrompt: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], JsonResponse::HTTP_BAD_REQUEST);

    } catch (\Exception $e) {
        $logger->error('Error in customPrompt: ' . $e->getMessage());
        return $this->json([
            'status' => 'error',
            'message' => 'Une erreur interne s\'est produite lors de la génération de la réponse personnalisée.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
