<?php

namespace App\Service;

use App\Entity\FailedLoginAttempt;
use App\Entity\LockedIpAddress;
use App\Repository\FailedLoginAttemptRepository;
use App\Repository\LockedIpAddressRepository;
use Doctrine\ORM\EntityManagerInterface;

class LockingService
{
    /**
     * @var FailedLoginAttemptRepository
     */
    private $failedLoginAttemptRepository;

    /**
     * @var LockedIpAddressRepository
     */
    private $lockedIpAddressRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * LockingService constructor.
     */
    public function __construct(
        FailedLoginAttemptRepository $failedLoginAttemptRepository,
        LockedIpAddressRepository $lockedIpAddressRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->failedLoginAttemptRepository = $failedLoginAttemptRepository;
        $this->lockedIpAddressRepository = $lockedIpAddressRepository;
        $this->entityManager = $entityManager;
    }

    public function addFailure(string $ipAddress): void
    {
        $failedLoginAttempt = $this->failedLoginAttemptRepository->findByIp($ipAddress);
        /** @var \DateTimeImmutable $currentTime */
        $currentTime = (new \DateTimeImmutable())::createFromFormat('U', (string) time());
        if (!$failedLoginAttempt) {
            $failedLoginAttempt = new FailedLoginAttempt(
                $ipAddress,
                $currentTime
            );
            $this->failedLoginAttemptRepository->add($failedLoginAttempt);
        } else {
            $failedLoginAttempt->addFailure((new \DateTimeImmutable())::createFromFormat('U', (string) time()));
            if ($failedLoginAttempt->isTooMany()) {
                $lockedIpAddress = $this->lockedIpAddressRepository->findByIp($ipAddress);
                if (!$lockedIpAddress) {
                    $lockedIpAddress = new LockedIpAddress(
                        $ipAddress,
                        $currentTime
                    );
                    $this->lockedIpAddressRepository->add($lockedIpAddress);
                } else {
                    $lockedIpAddress->setLockedSince((new \DateTimeImmutable())::createFromFormat('U', (string) time()));
                }
            }
        }
    }

    public function removeFailedIp(string $ipAddress): void
    {
        $this->failedLoginAttemptRepository->removeByIp($ipAddress);
        $this->lockedIpAddressRepository->removeByIp($ipAddress);
        $this->entityManager->flush();
    }

    public function checkIsLockedIpAddress(string $ipAddress): bool
    {
        $lockedIpAddress = $this->lockedIpAddressRepository->findByIp($ipAddress);
        if ($lockedIpAddress) {
            return $lockedIpAddress->isActiveAt((new \DateTimeImmutable())::createFromFormat('U', (string) time()));
        }

        return false;
    }
}
