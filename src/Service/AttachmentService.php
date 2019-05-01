<?php

namespace App\Service;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

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
     * @var string
     */
    private $tmpAttachmentsPath;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AttachmentService constructor.
     */
    public function __construct(
        string $attachmentsPath,
        string $tmpAttachmentsPath,
        EntityManagerInterface $defaultEntityManager
    ) {
        $this->attachmentsPath = $attachmentsPath;
        $this->tmpAttachmentsPath = $tmpAttachmentsPath;
        $this->entityManager = $defaultEntityManager;
    }

    /**
     * @param string $uploadFormId
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function moveAttachments($uploadFormId)
    {
        //$attachments = $this->attachmentRepository->getByUploadFormId($uploadFormId);
        foreach ($attachments as $attachment) {
            $fileName = pathinfo($attachment->getName(), PATHINFO_BASENAME);
            $attachment->setName($fileName);
            // and move the file
            $fileId = $attachment->getId();
            $dirToRemove = $this->tmpAttachmentsPath.\DIRECTORY_SEPARATOR.$attachment->getUniqueUploadId();
            $file = new File($dirToRemove.\DIRECTORY_SEPARATOR.$fileId);
            $newPath = $this->attachmentsPath.
                \DIRECTORY_SEPARATOR.$attachment->getArticle()->getId().\DIRECTORY_SEPARATOR;
            $file->move($newPath, $fileId);
            $fileSystem = new Filesystem();
            $fileSystem->remove($dirToRemove);
        }
    }

    /**
     * @return array|\Doctrine\DBAL\Driver\Statement|null
     */
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
