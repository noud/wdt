<?php

namespace App\Form\Handler;

use App\Form\Data\AttachmentRemoveNewData;
use App\Service\AttachmentService;
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

            $uploadFormId = $data->uploadFormId;
            $fileName = $data->uniqueUploadId;

            // remove from filesystem
            $this->attachmentService->removeAttachment($uploadFormId, $fileName);
            dump($fileName);

            return true;
            // //            $publicAttachments = $this->ticketAttachmentService->getAllPublicTicketAttachments($ticketId);
//             foreach ($publicAttachments as $publicAttachment) {
//                 if ($fileName === $publicAttachment['name']) {
//                     $attachmentId = $publicAttachment['id'];
//                     //$this->ticketAttachmentService->removeTicketAttachment($ticketId, $attachmentId);

//                     return true;
//                 }
//             }
        }

        return false;
    }
}
