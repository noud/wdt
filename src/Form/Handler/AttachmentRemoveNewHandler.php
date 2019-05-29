<?php

namespace App\Form\Handler;

use App\Form\Data\AttachmentRemoveNewData;
use App\Service\AttachmentService;
use App\Service\StringService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AttachmentRemoveNewHandler
{
    /**
     * @var AttachmentService
     */
    private $attachmentService;

    public function __construct(
        AttachmentService $attachmentService
    ) {
        $this->attachmentService = $attachmentService;
    }

    public function handleRequest(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AttachmentRemoveNewData $data */
            $data = $form->getData();

            // prevent climbing the path
            $uploadFormId = $data->uploadFormId;
            StringService::checkCharactersAndNumbersWithDot($uploadFormId);
            $fileName = $data->uniqueUploadId;
            $fileName = StringService::checkFilename($fileName);

            // remove from filesystem
            $this->attachmentService->removeAttachment($uploadFormId, $fileName);

            return true;
        }

        return false;
    }
}
