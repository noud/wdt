<?php

namespace App\Uploader\Naming;

use App\Service\AttachmentService;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class UploadNamer implements NamerInterface
{
    /**
     * @var AttachmentService
     */
    private $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Gets original name for the file being uploaded.
     *
     * @return string the directory name
     */
    public function name(FileInterface $file): string
    {
        $attachment = $this->attachmentService->createUploaded($file->getClientOriginalName());

        return $attachment->getUniqueUploadId().'/'.$attachment->getId();
    }
}
