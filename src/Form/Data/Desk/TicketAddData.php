<?php

namespace App\Form\Data\Desk;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class TicketAddData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $contactName;

    /**
     * @var string
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $subject;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $description;

    /**
     * @var string
     */
    public $priority;

    public function __construct(User $user)
    {
        $this->contactName = $user->getFullName();
        /** @var string $email */
        $email = $user->getEmail();
        $this->email = $email;
    }
}
