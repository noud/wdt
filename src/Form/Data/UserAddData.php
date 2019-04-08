<?php

namespace App\Form\Data;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ UniqueEntity("email")
 */
class UserAddData
{
    /**
     * @var string
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $passwordRepeat;
}
