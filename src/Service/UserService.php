<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Data\UserAddData;
use App\Mailer\MailSender;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

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
     * @var MailSender
     */
    private $mailSender;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailSender $mailSender
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->mailSender = $mailSender;
    }

    /**
     * @return User
     */
    public function add(UserAddData $data)
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

    /**
     * @return User
     */
    public function addAndEmail(UserAddData $data)
    {
        $user = $this->add($data);
        $this->entityManager->flush();
        $this->mailSender->sendUserAddedMessage('Gebruiker toegevoegd', $user);

        return $user;
    }

    public function activate(User $user): User
    {
        $user->setActive(true);
        $user->setToken('');

        return $user;
    }

    public function activateAndEmail(User $user): User
    {
        $user = $this->activate($user);
        $this->entityManager->flush();
        $this->mailSender->sendUserActivatedMessage('Gebruiker geactiveerd', $user);

        return $user;
    }
}
