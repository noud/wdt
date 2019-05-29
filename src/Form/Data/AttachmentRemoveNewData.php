<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class AttachmentRemoveNewData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $uploadFormId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $uniqueUploadId;
}
