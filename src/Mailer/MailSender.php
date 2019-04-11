<?php

namespace App\Mailer;

use App\Entity\User;
use App\Service\MailerService;

class MailSender
{
    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * MailSender constructor.
     */
    public function __construct(
        MailerService $mailerService
    ) {
        $this->mailerService = $mailerService;
    }

    public function sendUserAddedMessage(string $subject, User $user): void
    {
        $body = $this->mailerService->render('email/user/add.html.twig', [
            'user' => $user,
        ]);

        $message = $this->mailerService->createMessage($subject, $body, 'text/html');
        $message->addPart(
            $this->mailerService->render(
                'email/user/add.txt.twig',
                [
                    'user' => $user,
                ]
            ),
            'text/plain'
        );

        $message->addTo($this->mailerService->getDefaultFromEmail());
        $message->setReplyTo($this->mailerService->getDefaultFromEmail());

        $this->mailerService->sendMessage($message);
    }

    public function sendUserActivatedMessage(string $subject, User $user): void
    {
        $body = $this->mailerService->render('email/user/active.html.twig', [
            'user' => $user,
        ]);

        $message = $this->mailerService->createMessage($subject, $body, 'text/html');
        $message->addPart(
            $this->mailerService->render(
                'email/user/active.txt.twig',
                [
                    'user' => $user,
                ]
            ),
            'text/plain'
        );

        /** @var string $email */
        $email = $user->getEmail();
        $message->addTo($email);
        $message->setReplyTo($this->mailerService->getDefaultFromEmail());

        $this->mailerService->sendMessage($message);
    }
}
