<?php

namespace App\Form\Handler;

use App\Entity\ResetPasswordRequest;
use App\Form\Data\ResetPasswordData;
use App\Security\LoginAuthenticator;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ResetPasswordHandler
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var LoginAuthenticator
     */
    private $authenticator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var GuardAuthenticatorHandler
     */
    private $guardAuthenticatorHandler;

    public function __construct(
        UserService $userService,
        LoginAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        GuardAuthenticatorHandler $guardAuthenticatorHandler
    ) {
        $this->userService = $userService;
        $this->authenticator = $authenticator;
        $this->entityManager = $entityManager;
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request, ResetPasswordRequest $resetPasswordRequest): ?Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ResetPasswordData $data */
            $data = $form->getData();

            $this->entityManager->remove($resetPasswordRequest);
            $this->entityManager->flush();

            /** @var string $newPassword */
            $newPassword = $data->getPlainPassword();
            $user = $this->userService->changePassword($resetPasswordRequest->getUsername(), $newPassword);
            if ($user) {
                return $this->guardAuthenticatorHandler
                    ->authenticateUserAndHandleSuccess(
                        $user,
                        $request,
                        $this->authenticator,
                        'main'
                    );
            }
        }

        return null;
    }
}
