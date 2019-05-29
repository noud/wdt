<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class AttachmentRemoveEditData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $filename;
}
