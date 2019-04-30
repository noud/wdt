<?php

namespace App\Service;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class AttachmentService
 * @package App\Service
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
     * @param string $attachmentsPath
     * @param string $tmpAttachmentsPath
     * @param EntityManagerInterface $defaultEntityManager
     * @param AttachmentRepository $attachmentRepository
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
            $dirToRemove = $this->tmpAttachmentsPath . DIRECTORY_SEPARATOR . $attachment->getUniqueUploadId();
            $file = new File($dirToRemove . DIRECTORY_SEPARATOR . $fileId);
            $newPath = $this->attachmentsPath .
                DIRECTORY_SEPARATOR . $attachment->getArticle()->getId() . DIRECTORY_SEPARATOR;
            $file->move($newPath, $fileId);
            $fileSystem = new Filesystem();
            $fileSystem->remove($dirToRemove);
        }
    }

    /**
     * @param string $uniqueUploadId
     * @return null|array|\Doctrine\DBAL\Driver\Statement
     */
    public function removeAttachment($uniqueUploadId)
    {
        //$attachment = $this->attachmentRepository->getByUniqueUploadId($uniqueUploadId);
//         $attachmentId = $attachment->getId();
//         $article = $attachment->getArticle();
//         if ($attachment && $article) {
//             $articleId = $article->getId();
//             $fileSystem = new Filesystem();
//             $fileDir = $this->attachmentsPath . DIRECTORY_SEPARATOR . $articleId;
//             $fileSystem->remove($fileDir . DIRECTORY_SEPARATOR . $attachmentId);
//             if (file_exists($fileDir) && count(scandir($fileDir, SCANDIR_SORT_NONE)) === 2) {
//                 $fileSystem->remove($fileDir);
//             }
//             $this->entityManager->remove($attachment);
//         } elseif ($attachment) {
            $fileSystem = new Filesystem();
            $fileDir = $this->tmpAttachmentsPath . DIRECTORY_SEPARATOR . $attachment->getUniqueUploadId();
            $fileSystem->remove($fileDir . DIRECTORY_SEPARATOR . $attachmentId);
            $fileSystem->remove($fileDir);
//            $this->entityManager->remove($attachment);
//        }
        return $attachment;
    }
}