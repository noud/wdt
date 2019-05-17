<?php

namespace App\Service;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class MailerService
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $defaultFromEmail;

    /**
     * @var string
     */
    private $defaultFromName;

    public function __construct(
        Swift_Mailer $mailer,
        Twig_Environment $twig,
        string $defaultFromEmail,
        string $defaultFromName
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->defaultFromEmail = $defaultFromEmail;
        $this->defaultFromName = $defaultFromName;
    }

    /**
     * @throws Twig_Error_Loader  When the template cannot be found
     * @throws Twig_Error_Syntax  When an error occurred during compilation
     * @throws Twig_Error_Runtime When an error occurred during rendering
     */
    public function render(string $name, array $context = []): string
    {
        return $this->twig->render($name, $context);
    }

    /**
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function send($to, array $data, string $template = 'emails/message.html.twig'): int
    {
        // Create message
        $body = $this->render($template, $data);
        $mailMessage = $this->createMessage($data['subject'], $body, 'text/html');
        $mailMessage->addTo($to->getEmailAddress(), $to->getName());

        return $this->mailer->send($mailMessage);
    }

    public function sendMessage(Swift_Message $message): ?int
    {
        return $this->mailer->send($message);
    }

    public function createMessage(string $subject = null, string $body = null, string $contentType = null, string $charset = null): Swift_Message
    {
        $message = new Swift_Message($subject, $body, $contentType, $charset);
        $message->setFrom($this->defaultFromEmail, $this->defaultFromName);

        return $message;
    }

    public function getDefaultFromEmail(): string
    {
        return $this->defaultFromEmail;
    }

    public function getDefaultFromName(): string
    {
        return $this->defaultFromName;
    }
}
