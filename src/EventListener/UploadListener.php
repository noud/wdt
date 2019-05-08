<?php

namespace App\EventListener;

use App\Service\AttachmentService;
use Doctrine\ORM\EntityManagerInterface;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
        $targetFile = $file->getPathName();
        $fileSize = $file->getSize();
        $targetFileArr = explode('/', $targetFile);
        $uniqueUploadId = $targetFileArr[\count($targetFileArr) - 1];

        // place the file in the uploadFormId dir
        try {
            $finalPath = $this->attachmentsDirectoryPart.\DIRECTORY_SEPARATOR.$uploadFormId;
            $filesystem = new Filesystem();
            $filesystem->mkdir($finalPath, 0700);
            $file->move(
                $finalPath,
                $uniqueUploadId
            );
        } catch (FileException $e) {
            throw new \Exception('Error moving uploaded file.');
        }

        //if everything went fine
        $response = $event->getResponse();
        $response[] = [
            'success' => true,
            'upload_form_id' => $uploadFormId,
            'unique_upload_id' => $uniqueUploadId,
            'target_file' => $targetFile,
            'target_size' => $fileSize,
            'file_name' => $fileName,
        ];
        $filePathName = $file->getPathName();
        $response['target_url'] = mb_substr(
            $filePathName,
            mb_strpos($filePathName, $this->attachmentsDirectoryPart) + mb_strlen($this->attachmentsDirectoryPart)
        );

        return $response;
    }
}
