<?php

namespace App\Tests\Service;

use App\Entity\FailedLoginAttempt;
use App\Repository\FailedLoginAttemptRepository;
use App\Repository\LockedIpAddressRepository;
use App\Service\LockingService;
use App\Tests\ServiceKernelTestCase;

/**
 * @group time-sensitive
 */
class LockingServiceTest extends ServiceKernelTestCase
{
    private const IP_ADDRESS = '127.0.0.1';

    /**
     * @var LockingService
     */
    private $lockingService;

    /**
     * @var FailedLoginAttemptRepository
     */
    private $failedLoginAttemptRepository;

    /**
     * @var LockedIpAddressRepository
     */
    private $lockedIpAddressRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->lockingService = self::$container->get(LockingService::class);
        $this->failedLoginAttemptRepository = self::$container->get(FailedLoginAttemptRepository::class);
        $this->lockedIpAddressRepository = self::$container->get(LockedIpAddressRepository::class);
    }

    /**
     * Test adding a failure.
     */
    public function testAddFailure(): void
    {
        $this->lockingService->addFailure(self::IP_ADDRESS);
        $this->entityManager->flush();
        /** @var FailedLoginAttempt $failedLoginAttempt */
        $failedLoginAttempt = $this->failedLoginAttemptRepository->findByIp(self::IP_ADDRESS);
        $ipAddressFound = $failedLoginAttempt->getIpAddress();
        $this->assertSame(self::IP_ADDRESS, $ipAddressFound);
    }

    /**
     * Test adding a second failure.
     */
    public function testAddSecondFailure(): void
    {
        $this->lockingService->addFailure(self::IP_ADDRESS);
        $this->entityManager->flush();
        $this->lockingService->addFailure(self::IP_ADDRESS);
        $this->entityManager->flush();
        /** @var FailedLoginAttempt $failedLoginAttempt */
        $failedLoginAttempt = $this->failedLoginAttemptRepository->findByIp(self::IP_ADDRESS);
        $numberOfFailures = $failedLoginAttempt->getNumberOfFailures();
        $this->assertSame(2, $numberOfFailures);
    }

    /**
     * Test removing a failure.
     */
    public function testRemoveFailedIp(): void
    {
        $failedLoginAttempt = new FailedLoginAttempt(self::IP_ADDRESS, new \DateTimeImmutable());
        $this->failedLoginAttemptRepository->add($failedLoginAttempt);
        $this->entityManager->flush();

        $this->lockingService->removeFailedIp(self::IP_ADDRESS);
        $failedLoginAttempt = $this->failedLoginAttemptRepository->findByIp(self::IP_ADDRESS);
        $this->assertNull($failedLoginAttempt);
    }

    /**
     * Test check is locked ip address.
     */
    public function testCheckIsLockedIpAddress(): void
    {
        for ($i = 1; $i <= FailedLoginAttempt::NUMBER_OF_ALLOWED_FAILURES + 1; ++$i) {
            $this->lockingService->addFailure(self::IP_ADDRESS);
            $this->entityManager->flush();
        }
        sleep(14 * 60); // 14 mimutes later

        $result = $this->lockingService->checkIsLockedIpAddress(self::IP_ADDRESS);
        $this->assertTrue($result);
    }

    /**
     * Test check is not locked ip address.
     */
    public function testCheckIsNotLockedIpAddress(): void
    {
        for ($i = 1; $i <= FailedLoginAttempt::NUMBER_OF_ALLOWED_FAILURES + 1; ++$i) {
            $this->lockingService->addFailure(self::IP_ADDRESS);
            $this->entityManager->flush();
        }
        sleep(16 * 60); // 16 minutes later

        $result = $this->lockingService->checkIsLockedIpAddress(self::IP_ADDRESS);
        $this->assertFalse($result);
    }
}
