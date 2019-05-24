<?php

namespace App\EventListener;

use App\Service\AttachmentService;
use App\Service\StringService;
use Doctrine\ORM\EntityManagerInterface;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadListener implements EventSubscriberInterface
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
        string $ticketAttachmentPath,
        EntityManagerInterface $defaultEntityManager,
        AttachmentService $attachmentService
    ) {
        $this->attachmentsDirectoryPart = $ticketAttachmentPath;
        $this->entityManager = $defaultEntityManager;
        $this->attachmentService = $attachmentService;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'oneup_uploader.post_persist' => 'onUpload',
        ];
    }

    /**
     * @return \Oneup\UploaderBundle\Uploader\Response\ResponseInterface
     */
    public function onUpload(PostPersistEvent $event)
    {
        $request = $event->getRequest();
        // prevent climbing the path
        $uploadFormId = $request->get('uploadFormId');
        StringService::checkCharactersAndNumbersWithDot($uploadFormId);

        $fileName = $event->getRequest()->get('filename');
        $fileName = StringService::checkFilename($fileName);

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
        $response['success'] = true;
        $response['upload_form_id'] = $uploadFormId;
        $response['unique_upload_id'] = $uniqueUploadId;
        $response['target_file'] = $targetFile;
        $response['target_size'] = $fileSize;
        $response['file_name'] = $fileName;
        $filePathName = $file->getPathName();
        $response['target_url'] = mb_substr(
            $filePathName,
            mb_strpos($filePathName, $this->attachmentsDirectoryPart) + mb_strlen($this->attachmentsDirectoryPart)
        );

        return $response;
    }
}
