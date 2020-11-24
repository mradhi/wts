<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param User $one
     * @param User $two
     *
     * @return Message[]
     */
    public function findDiscussion(User $one, User $two): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('o.receiver', ':one'),
                    $queryBuilder->expr()->eq('o.receiver', ':two')
                )
            )
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('o.sender', ':one'),
                    $queryBuilder->expr()->eq('o.sender', ':two')
                )
            )
            ->setParameters([
                'one' => $one,
                'two' => $two
            ])
            ->addOrderBy('o.createdAt', 'ASC');

        return $queryBuilder->getQuery()
            ->getResult();
    }
}