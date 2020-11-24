<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointVoteRepository")
 * @ORM\Table(name="points_votes")
 */
class PointVote
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
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected ?User $user = null;

    /**
     * @var Point|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Point", inversedBy="votes")
     * @ORM\JoinColumn(name="point_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected ?Point $point = null;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected int $value = 0;

    /**
     * @var DateTime|DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @param User|UserInterface  $user
     * @param Point $point
     */
    public function doUpVote(User $user, Point $point): void
    {
        $this->doVote($user, $point, 1);
    }

    /**
     * @param User|UserInterface  $user
     * @param Point $point
     */
    public function doDownVote(User $user, Point $point): void
    {
        $this->doVote($user, $point, -1);
    }

    public function doVote(User $user, Point $point, int $value): void
    {
        $this->setUser($user);
        $this->setPoint($point);
        $this->setValue($value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getPoint(): ?Point
    {
        return $this->point;
    }

    public function setPoint(?Point $point): void
    {
        $point->addVote($this);
        $this->point = $point;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }
}