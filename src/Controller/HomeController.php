<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();
        $message = 'Bienvenue sur la page d\'accueil !';
    
        if ($user) {
            $message .= ' ' . $user->getUserIdentifier();
        }
    
        return $this->render('home/index.html.twig', [
            'message' => $message,
            'user' => $user,
        ]);
    }
}