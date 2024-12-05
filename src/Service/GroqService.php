<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GroqService
{
    private $httpClient;
    private $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function sendRequest(array $formattedMessages): array
    {
        $messagesPayload = array_map(function ($message) {
            return [
                'role' => 'user', // Ici, vous pouvez ajuster le rôle si nécessaire
                'content' => $message
            ];
        }, $formattedMessages);
    
        $payload = [
            'messages' => $messagesPayload,
            'model' => 'mixtral-8x7b-32768' // Modèle spécifié dans la documentation
        ];
    
        $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
            'json' => $payload
        ]);
    
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new \Exception("Erreur API, code : $statusCode. Réponse : " . $response->getContent(false));
        }
    
        return json_decode($response->getContent(), true);
    }
}
