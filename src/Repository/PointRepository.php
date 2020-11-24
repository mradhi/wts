<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Point;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Point::class);
    }

    /**
     * @return Point[]
     */
    public function findRecent(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }
}