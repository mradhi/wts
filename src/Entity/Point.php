<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointRepository")
 * @ORM\Table(name="points")
 */
class Point
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
    protected ?User $user;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    protected ?string $identifier;

    /**
     * @var float|null
     *
     * @ORM\Column(name="coordinate_x", type="float")
     */
    protected ?float $x = null;

    /**
     * @var float|null
     *
     * @ORM\Column(name="coordinate_y", type="float")
     */
    protected ?float $y = null;

    /**
     * @var float|null
     *
     * @ORM\Column(name="coordinate_z", type="float", nullable=true)
     */
    protected ?float $z = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $description;

    /**
     * @var Collection|PointVote[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PointVote", mappedBy="point")
     */
    protected Collection $votes;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->createdAt = new DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(?float $x): void
    {
        $this->x = $x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(?float $y): void
    {
        $this->y = $y;
    }

    public function getZ(): ?float
    {
        return $this->z;
    }

    public function setZ(?float $z): void
    {
        $this->z = $z;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|UserInterface|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUserVote(UserInterface $user): ?PointVote
    {
        foreach ($this->votes as $vote) {
            if ($user === $vote->getUser()) {
                return $vote;
            }
        }

        return null;
    }

    public function isUserAlreadyVoted(UserInterface $user): bool
    {
        return null !== $this->getUserVote($user);
    }

    public function getReliability(): float
    {
        $positiveVotes = 0;
        $total = 0;

        foreach ($this->votes as $vote) {
            if ($vote->isPositive()) {
                $positiveVotes++;
            }

            $total++;
        }

        if ($total === 0) {
            return 0;
        }

        return (int) (($positiveVotes / $total) * 100);
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(PointVote $vote): void
    {
        if ($this->votes->contains($vote)) {
            return;
        }

        $this->votes->add($vote);
    }

    public function removeVote(PointVote $vote): void
    {
        if (!$this->votes->contains($vote)) {
            return;
        }

        $this->votes->removeElement($vote);
    }
}