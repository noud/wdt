<?php

namespace App\Repository;

use App\Entity\ResetPasswordRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ResetPasswordRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPasswordRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetPasswordRequest[]    findAll()
 * @method ResetPasswordRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetPasswordRequestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }

    public function findByValidToken(string $token): ?ResetPasswordRequest
    {
        $now = (new \DateTimeImmutable())::createFromFormat('U', (string) time());

        return $this->createQueryBuilder('reset_password_request')
            ->where('reset_password_request.token = :token')
            ->setParameter(':token', $token)
            ->andWhere('reset_password_request.expireDate >= :now')
            ->setParameter(':now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removeExpired(): void
    {
        $this->removeExpiredByUsername();
    }

    public function removeExpiredByUsername(string $username = null): void
    {
        $now = (new \DateTimeImmutable())::createFromFormat('U', (string) time());

        $queryBuilder = $this->createQueryBuilder('reset_password_request')
            ->delete()
            ->where('reset_password_request.expireDate < :now')
            ->setParameter(':now', $now);

        if ($username) {
            $queryBuilder->andWhere('reset_password_request.username = :username')
                ->setParameter(':username', $username);
        }

        $query = $queryBuilder->getQuery();
        $query->getResult();
    }
}
