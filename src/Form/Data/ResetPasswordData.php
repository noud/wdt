<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordData
{
    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "password_length",
     * )
     * @Assert\NotBlank
     */
    private $plainPassword;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }
}
