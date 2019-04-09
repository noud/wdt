<?php

namespace App\Tests\Service;

use App\Service\MailerService;
use App\Tests\ServiceKernelTestCase;

class MailerServiceTest extends ServiceKernelTestCase
{
    private const EMAIL_ADDRESS = 'info@webdesigntilburg.nl';
    private const EMAIL_NAME = 'Webdesign Tilburg';

    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->mailerService = self::$container->get(MailerService::class);
    }

    /**
     * Test getting default email address.
     */
    public function testgetDefaultFromEmail(): void
    {
        $email = $this->mailerService->getDefaultFromEmail();

        $this->assertSame(self::EMAIL_ADDRESS, $email);
    }

    /**
     * Test getting default email name.
     */
    public function testgetDefaultFromName(): void
    {
        $email = $this->mailerService->getDefaultFromName();

        $this->assertSame(self::EMAIL_NAME, $email);
    }
}
