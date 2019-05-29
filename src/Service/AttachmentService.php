<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class AttachmentService
{
    /**
     * @var string
     */
    private $attachmentsPath;

    public function __construct(
        string $ticketAttachmentPath
    ) {
        $this->attachmentsPath = $ticketAttachmentPath;
    }

    private function removeDirectoryIfEmpty(string $path): void
    {
        $fileSystem = new Filesystem();
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
        if (file_exists($fileDir.$attachmentId)) {
            $fileSystem->remove($fileDir.$attachmentId);
        }
        $this->removeDirectoryIfEmpty($fileDir);
    }
}
