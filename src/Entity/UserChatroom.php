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
    private ?User $_user = null;

    #[ORM\ManyToOne(inversedBy: 'userChatrooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chatroom $_chatroom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastRead = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function setUser(?User $_user): static
    {
        $this->_user = $_user;

        return $this;
    }

    public function getChatroom(): ?Chatroom
    {
        return $this->_chatroom;
    }

    public function setChatroom(?Chatroom $_chatroom): static
    {
        $this->_chatroom = $_chatroom;

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
