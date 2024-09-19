<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param array<array-key, mixed> $filters
     *
     * @return array<int, Message>
     */
    public function by(array $filters): array
    {
        $status = $filters['status'] ?? null;
        if (null === $status || '' === $status) {
            return $this->findAll();
        }

        $expressionBuilder = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('message')
//            ->where('message.status = :status') // simpler, but doesn't allow nesting of complex queries
            ->where(
                $expressionBuilder->eq('message.status', ':status'),
            )
            ->setParameter('status', $status)
            ->getQuery()->getResult();
    }
}
