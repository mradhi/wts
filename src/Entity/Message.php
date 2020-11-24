<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 * @ORM\Table(name="messages")
 */
class Message
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected ?User $sender = null;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="receiver_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected ?User $receiver = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text")
     */
    protected ?string $message = null;

    /**
     * @var DateTime|DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    protected ?DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): void
    {
        $this->sender = $sender;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}