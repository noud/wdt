<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class UserAddData
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $companyName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $password;

    /**
     * @var string
     */
    public $passwordRepeat;
}
