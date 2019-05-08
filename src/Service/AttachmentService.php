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

    private function removeDirectoryIfEmpty(string $path): void
    {
        $files = Finder::create()
            ->files()
            ->in($path);
        if (0 === \count($files)) {
            $fileSystem->remove($path);
        }
    }
    
    public function removeAttachment(string $uploadFormId, string $attachmentId): void
    {
        $fileSystem = new Filesystem();
        $fileDir = $this->attachmentsPath.\DIRECTORY_SEPARATOR.$uploadFormId.\DIRECTORY_SEPARATOR;
        $fileSystem->remove($fileDir.$attachmentId);
        $this->removeDirectoryIfEmpty($fileDir);
    }
}
