<?php

namespace App\Tests\Service;

use App\Entity\ResetPasswordRequest;
use App\Service\ResetPasswordRequestService;
use App\Tests\ServiceKernelTestCase;

/**
 * @group time-sensitive
 */
class ResetPasswordRequestServiceTest extends ServiceKernelTestCase
{
    private const USERNAME = 'tst@dev.nl';

    /**
     * @var ResetPasswordRequestService
     */
    private $resetPasswordRequestService;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->resetPasswordRequestService = self::$container->get(ResetPasswordRequestService::class);
    }

    /**
     * Test adding a Reset Password Request.
     */
    public function testAddResetPasswordRequest(): void
    {
        $token = $this->resetPasswordRequestService->addResetPasswordRequest(self::USERNAME);
        $this->entityManager->flush();
        $this->assertNotNull($token);
    }

    /**
     * Test finding a Reset Password Request token.
     */
    public function testFindValidToken(): void
    {
        $token = $this->resetPasswordRequestService->addResetPasswordRequest(self::USERNAME);
        $this->entityManager->flush();
        /** @var ResetPasswordRequest $resetPasswordRequest */
        $resetPasswordRequest = $this->resetPasswordRequestService->findValidToken($token);
        $username = $resetPasswordRequest->getUsername();
        $this->assertSame(self::USERNAME, $username);
    }

    /**
     * Test removing expired Reset Password Request unsuccessfull.
     */
    public function testUnuccesfullRemoveExpired(): void
    {
        $token = $this->resetPasswordRequestService->addResetPasswordRequest(self::USERNAME);
        $this->entityManager->flush();
        $this->resetPasswordRequestService->removeExpired();
        $resetPasswordRequest = $this->resetPasswordRequestService->findValidToken($token);
        $this->assertNotNull($resetPasswordRequest);
    }

    /**
     * Test removing expired Reset Password Request successfull.
     */
    public function testSuccesfullRemoveExpired(): void
    {
        $token = $this->resetPasswordRequestService->addResetPasswordRequest(self::USERNAME);
        $this->entityManager->flush();

        sleep(2 * 24 * 60 * 60); // 2 days later

        $this->resetPasswordRequestService->removeExpired();
        $resetPasswordRequest = $this->resetPasswordRequestService->findValidToken($token);
        $this->assertNull($resetPasswordRequest);
    }
}
