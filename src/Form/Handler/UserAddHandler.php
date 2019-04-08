<?php

namespace App\Form\Handler;

use App\Mailer\MailSender;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UserAddHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var MailSender
     */
    private $mailSender;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserService $userService,
        MailSender $mailSender
    ) {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->mailSender = $mailSender;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Form\Data\UserAddData $userData */
            $userData = $form->getData();
            $user = $this->userService->add($userData);

            $this->entityManager->flush();
            $this->mailSender->sendUserAddedMessage('Gebruiker toegevoegd', $user);

            return true;
        }

        return false;
    }
}
