<?php

namespace App\Security;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserProvider constructor.
     */
    public function __construct(UserService $userService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * @param string $email
     */
    public function loadUserByUsername($email): ?User
    {
        return $this->userService->loadUserByEmail($email);
    }

    /**
     * @throws \Exception
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        $user = $this->loadUserByUsername($user->getUsername());
        if (!$user instanceof User) {
            throw new UnsupportedUserException($this->translator->trans('login.messages.invalid_user_class', ['%user_class%' => User::class], 'login'));
        }

        if (null !== $user) {
            return $user;
        }
        throw new UsernameNotFoundException();
    }

    /**
     * @param string $class
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
