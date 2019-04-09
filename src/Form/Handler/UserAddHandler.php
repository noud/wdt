<?php

namespace App\Form\Handler;

use App\Service\UserService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UserAddHandler
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
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
            $this->userService->addAndEmail($userData);

            return true;
        }

        return false;
    }
}
