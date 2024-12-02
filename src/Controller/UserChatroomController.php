<?php

namespace App\Controller;

use App\Entity\UserChatroom;
use App\Form\UserChatroomType;
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

    #[Route('/new', name: 'app_user_chatroom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userChatroom = new UserChatroom();
        $form = $this->createForm(UserChatroomType::class, $userChatroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userChatroom);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_chatroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_chatroom/new.html.twig', [
            'user_chatroom' => $userChatroom,
            'form' => $form,
        ]);
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
