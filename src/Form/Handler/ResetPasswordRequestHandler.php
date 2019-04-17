<?php

namespace App\Form\Handler;

use App\Form\Data\RequestResetPasswordData;
use App\Service\ResetPasswordRequestService;
use App\Service\UserService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordRequestHandler
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ResetPasswordRequestService
     */
    private $resetPasswordRequestService;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UserService $userService,
        ResetPasswordRequestService $resetPasswordRequestService,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->userService = $userService;
        $this->resetPasswordRequestService = $resetPasswordRequestService;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function handleRequest(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RequestResetPasswordData $data */
            $data = $form->getData();

            /** @var string $username */
            $username = $data->getUsername();

            $this->flashBag->add('success',
                $this->translator->trans(
                    'reset_password.message.reset_password_message %name%',
                    ['%name%' => $username],
                    'reset_password'
                )
            );

            if ($this->userService->loadUserByEmail($username)) {
                $this->resetPasswordRequestService->addResetPasswordRequest($username);
            }

            return true;
        }

        return false;
    }
}
