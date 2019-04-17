<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Mailer\MailSender;
use App\Service\MailerService;
use App\Tests\ServiceKernelTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swift_Mailer;

class MailerServiceTest extends ServiceKernelTestCase
{
    private const EMAIL_ADDRESS = 'info@webdesigntilburg.nl';
    private const EMAIL_NAME = 'Webdesign Tilburg';

    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * @var MockObject
     */
    private $mailer;

    /**
     * @var MailSender
     */
    private $mailSender;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->mailer = $this->createMock(Swift_Mailer::class);

        $this->mailerService = new MailerService(
            $this->mailer,
            self::$container->get('twig'),
            self::$container->getParameter('mailer_default_from_email'),
            self::$container->getParameter('mailer_default_from_name')
        );

        $this->mailSender = new MailSender($this->mailerService);
    }

    public function testSendUserAddedMessage()
    {
        $this->mailer
            ->expects($this->once())
            ->method('send');

        $user = new User();
        $user->setEmail('test@test.nl');
        $user->setCompanyName('test');
        $user->setFirstName('First');
        $user->setLastName('Last');
        $user->setToken('123');
        $this->mailSender->sendUserAddedMessage('new user', $user);
    }
}
