<?php

namespace App\Repository;

use App\Entity\FailedLoginAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FailedLoginAttemptRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FailedLoginAttempt::class);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(FailedLoginAttempt $failedLoginAttempt): void
    {
        $this->getEntityManager()->persist($failedLoginAttempt);
    }

    public function findByIp(string $ip): ?FailedLoginAttempt
    {
        /** @var FailedLoginAttempt $failedLoginAttempt */
        $failedLoginAttempt = $this->findOneBy(['ipAddress' => $ip]);

        return $failedLoginAttempt;
    }

    public function removeByIp(string $ip): void
    {
        $failedLoginAttempt = $this->findOneBy(['ipAddress' => $ip]);
        if ($failedLoginAttempt) {
            $this->getEntityManager()->remove($failedLoginAttempt);
        }
    }
}
