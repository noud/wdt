<?php

namespace App\Zoho\Form\Data\Desk;

use Symfony\Component\Validator\Constraints as Assert;

class TicketAttachmentAddData
{
    /**
     * @var bool
     * @Assert\NotBlank()
     */
    public $isPublic;
}
