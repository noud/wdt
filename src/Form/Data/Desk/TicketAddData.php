<?php

namespace App\Form\Data\Desk;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class TicketAddData
{
    /**
     * @var int
     */
    public $uploadFormId;
    
    /**
     * @var ArrayCollection
     */
    public $attachments;
    
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
        $this->uploadFormId = uniqid('', true);
        $this->attachments = new ArrayCollection();
        $this->contactName = $user->getFullName();
        /** @var string $email */
        $email = $user->getEmail();
        $this->email = $email;
    }
}
