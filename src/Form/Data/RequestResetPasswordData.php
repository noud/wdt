<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class RequestResetPasswordData
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $username;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
}
