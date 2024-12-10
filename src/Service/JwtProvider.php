<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class JwtProvider
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function createJwt(): string
    {
        try {
            $config = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText($this->secret)
            );
    
            $token = $config->builder()
                ->withClaim('mercure', ['subscribe' => ['https://localhost:8001/chat/{id}']])
                ->getToken($config->signer(), $config->signingKey());
    
            return $token->toString();
        } catch (\Exception $e) {
            throw new \RuntimeException('JWT creation failed: ' . $e->getMessage());
        }
    }
    
}