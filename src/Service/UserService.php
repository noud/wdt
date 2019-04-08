<?php

namespace App\Service;

use App\Entity\User;
use App\Form\Data\UserAddData;
use App\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
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
        $user->setToken(uniqid('', true));

        $this->userRepository->add($user);

        return $user;
    }

    /**
     * @return User
     */
    public function activate(User $user)
    {
        $user->setActive(true);
        $user->setToken('');

        return $user;
    }
}
