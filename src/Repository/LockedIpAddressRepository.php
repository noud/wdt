<?php

namespace App\Repository;

use App\Entity\LockedIpAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LockedIpAddressRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LockedIpAddress::class);
    }

    public function findByIp(string $ip): ?LockedIpAddress
    {
        /** @var LockedIpAddress $lockedIpAddress */
        $lockedIpAddress = $this->findOneBy(['ipAddress' => $ip]);

        return $lockedIpAddress;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(LockedIpAddress $lockedIpAddress): void
    {
        $this->getEntityManager()->persist($lockedIpAddress);
    }

    public function removeByIp(string $ip): void
    {
        $lockedIpAddress = $this->findOneBy(['ipAddress' => $ip]);
        if ($lockedIpAddress) {
            $this->getEntityManager()->remove($lockedIpAddress);
        }
    }
}
