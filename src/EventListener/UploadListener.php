<?php

namespace App\EventListener;

use App\Service\AttachmentService;
use Doctrine\ORM\EntityManagerInterface;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Filesystem\Filesystem;

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

        $fileName = $event->getRequest()->get('filename');
        
        $file = $event->getFile();
        $targetFile = $event->getFile()->getPathName();
        $fileSize = $event->getFile()->getSize();
        $targetFileArr = explode('/', $targetFile);
        $uniqueUploadId = $targetFileArr[\count($targetFileArr) - 1];

        // place the file in the uploadFormId dir
        try {
            $finalPath = $this->attachmentsDirectoryPart . '/' . $uploadFormId;
            $filesystem = new Filesystem();
            $filesystem->mkdir($finalPath, 0700);
            $file->move(
                $finalPath,
                $uniqueUploadId
            );
        } catch (FileException $e) {
            throw error \Exception('Error moving uploaded file.');
        }

        //if everything went fine
        $response = $event->getResponse();
        $response['success'] = true;
        $response['upload_form_id'] = $uploadFormId;
        $response['unique_upload_id'] = $uniqueUploadId;
        $response['target_file'] = $targetFile;
        $response['file_name'] = $fileName;
        $filePathName = $event->getFile()->getPathName();
        $response['target_url'] = mb_substr(
            $filePathName,
            mb_strpos($filePathName, $this->attachmentsDirectoryPart) + \mb_strlen($this->attachmentsDirectoryPart)
        );
        $response['target_size'] = $fileSize;

        return $response;
    }
}
