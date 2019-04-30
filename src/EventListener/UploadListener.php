<?php

namespace App\EventListener;

use App\Service\AttachmentService;
use Doctrine\ORM\EntityManagerInterface;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $attachmentsDirectoryPart;

    /**
     * @var AttachmentService
     */
    private $attachmentService;

    public function __construct(
        string $attachmentsDirectoryPart,
        EntityManagerInterface $defaultEntityManager,
        AttachmentService $attachmentService
    ) {
        $this->attachmentsDirectoryPart = $attachmentsDirectoryPart;
        $this->entityManager = $defaultEntityManager;
        $this->attachmentService = $attachmentService;
    }

    /**
     * @return \Oneup\UploaderBundle\Uploader\Response\ResponseInterface
     */
    public function onUpload(PostPersistEvent $event)
    {
        $request = $event->getRequest();
        $uploadFormId = $request->get('uploadFormId');

        $targetFile = $event->getFile()->getPathName();
        $targetFileArr = explode('/', $targetFile);
        $uniqueUploadId = $targetFileArr[\count($targetFileArr) - 2];

        // seek by $uniqueUploadId and set $uploadFormId
        $attachment = $this->attachmentService->getByUniqueUploadId($uniqueUploadId);
        $attachment->setUploadFormId($uploadFormId);
        $this->entityManager->persist($attachment);
        $this->entityManager->flush();

        //if everything went fine
        $response = $event->getResponse();
        $response['success'] = true;
        $response['unique_upload_id'] = $uniqueUploadId;
        $response['target_file'] = $targetFile;
        $filePathName = $event->getFile()->getPathName();
        $response['target_url'] = mb_substr(
            $filePathName,
            mb_strpos($filePathName, $this->attachmentsDirectoryPart) + \mb_strlen($this->attachmentsDirectoryPart)
        );
        $response['target_size'] = $event->getFile()->getSize();

        return $response;
    }
}
