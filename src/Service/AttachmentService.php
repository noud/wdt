<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class AttachmentService.
 */
class AttachmentService
{
    /**
     * @var string
     */
    private $attachmentsPath;

    /**
     * AttachmentService constructor.
     */
    public function __construct(
        string $ticketAttachmentPath
    ) {
        $this->attachmentsPath = $ticketAttachmentPath;
    }

    public function removeAttachment(string $uploadFormId, string $attachmentId): void
    {
        $fileSystem = new Filesystem();
        $fileDir = $this->attachmentsPath.\DIRECTORY_SEPARATOR.$uploadFormId;
        $fileSystem->remove($fileDir.\DIRECTORY_SEPARATOR.$attachmentId);

        $dirName = $fileDir.\DIRECTORY_SEPARATOR;
        $files = Finder::create()
            ->files()
            ->in($dirName);
        if (0 === \count($files)) {
            $fileSystem->remove($fileDir);
        }
    }
}
