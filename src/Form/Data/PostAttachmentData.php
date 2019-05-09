<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class PostAttachmentData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $filename;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    public $filesize;

    /**
     * @var string
     * @Assert\File(mimeTypes={ "application/pdf", "image/png", "image/gif", "image/jpeg" })
     */
    public $file;
}
