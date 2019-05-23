<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Data\UserAddData;
use App\Mailer\MailSender;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var MailSender
     */
    private $mailSender;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        MailSender $mailSender
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->mailSender = $mailSender;
    }

    public function add(UserAddData $data): User
    {
        $user = new User();
        $user->setEmail($data->email);
        $user->setCompanyName($data->companyName);
        $user->setFirstName($data->firstName);
        $user->setLastName($data->lastName);
        $user->setPlainPassword($data->password);
        $user->setToken(uniqid('', true));

        $this->userRepository->add($user);

        return $user;
    }

    public function addAndEmail(UserAddData $data): User
    {
        $user = $this->add($data);
        $this->entityManager->flush();
        $this->mailSender->sendUserAddedMessage('Gebruiker toegevoegd', $user);

        return $user;
    }

    public function activate(User $user): User
    {
        $user->setActive(true);
        $user->setToken(null);

        return $user;
    }

    public function activateAndEmail(User $user): User
    {
        $user = $this->activate($user);
        $this->entityManager->flush();
        $this->mailSender->sendUserActivatedMessage('Je account is geactiveerd', $user);

        return $user;
    }

    public function logoutUser(): void
    {
        $this->tokenStorage->setToken(null);
    }

    public function loadUserByEmail(string $email): ?User
    {
        /** @var \App\Entity\User $user */
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        return $user;
    }

    public function changePassword(string $email, string $newPassword): ?User
    {
        $user = $this->loadUserByEmail($email);
        if ($user) {
            $user->setPlainPassword($newPassword);
            $this->entityManager->flush();
        }

        return $user;
    }
}
