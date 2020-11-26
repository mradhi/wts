<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Point;
use App\Entity\PointVote;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class PointVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointVote::class);
    }

    /**
     * @param User|UserInterface  $user
     * @param Point $point
     *
     * @return PointVote|null
     */
    public function findOneByUser(User $user, Point $point): ?PointVote
    {
        return $this->findOneBy(['user' => $user, 'point' => $point]);
    }
}