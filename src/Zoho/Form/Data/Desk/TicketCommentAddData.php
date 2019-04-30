<?php

namespace App\Zoho\Form\Data\Desk;

use Symfony\Component\Validator\Constraints as Assert;

class TicketCommentAddData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $content;
}
