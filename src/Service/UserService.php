<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Data\UserAddData;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
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

        return $user;
    }
}
