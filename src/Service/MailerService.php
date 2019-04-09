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

    /**
     * @param string $defaultFromEmail
     * @param string $defaultFromName
     */
    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, $defaultFromEmail, $defaultFromName)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->defaultFromEmail = $defaultFromEmail;
        $this->defaultFromName = $defaultFromName;
    }

    /**
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @throws Twig_Error_Loader  When the template cannot be found
     * @throws Twig_Error_Syntax  When an error occurred during compilation
     * @throws Twig_Error_Runtime When an error occurred during rendering
     *
     * @return string The rendered template
     */
    public function render(string $name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }

    /**
     * @param array  $data
     * @param string $template
     *
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     *
     * @return int
     *
     * @internal param Swift_Message $message
     */
    public function send($to, $data, $template = 'emails/message.html.twig')
    {
        // Create message
        $body = $this->render($template, $data);
        $mailMessage = $this->createMessage($data['subject'], $body, 'text/html');
        $mailMessage->addTo($to->getEmailAddress(), $to->getName());

        return $this->mailer->send($mailMessage);
    }

    /**
     * @return int
     */
    public function sendMessage(Swift_Message $message)
    {
        return $this->mailer->send($message);
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return Swift_Message The message
     */
    public function createMessage($subject = null, $body = null, $contentType = null, $charset = null)
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
