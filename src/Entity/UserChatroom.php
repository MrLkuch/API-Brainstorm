<?php

namespace App\Entity;

use App\Repository\UserChatroomRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserChatroomRepository::class)]
class UserChatroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userChatrooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userChatrooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chatroom $chatroom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastRead = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getChatroom(): ?Chatroom
    {
        return $this->chatroom;
    }

    public function setChatroom(?Chatroom $chatroom): static
    {
        $this->chatroom = $chatroom;

        return $this;
    }

    public function getLastRead(): ?\DateTimeInterface
    {
        return $this->lastRead;
    }

    public function setLastRead(?\DateTimeInterface $lastRead): static
    {
        $this->lastRead = $lastRead;

        return $this;
    }
}
