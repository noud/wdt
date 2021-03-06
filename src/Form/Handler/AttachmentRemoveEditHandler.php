<?php

namespace App\Form\Handler;

use App\Form\Data\AttachmentRemoveEditData;
use App\Service\StringService;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AttachmentRemoveEditHandler
{
    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    public function __construct(
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->ticketAttachmentService = $ticketAttachmentService;
    }

    public function handleRequest(FormInterface $form, Request $request, int $ticketId): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AttachmentRemoveEditData $data */
            $data = $form->getData();

            $fileName = $data->filename;
            $fileName = StringService::checkFilename($fileName);
            $publicAttachments = $this->ticketAttachmentService->getAllPublicTicketAttachments($ticketId);
            foreach ($publicAttachments as $publicAttachment) {
                if ($fileName === $publicAttachment['name']) {
                    $attachmentId = $publicAttachment['id'];
                    $this->ticketAttachmentService->removeTicketAttachment($ticketId, $attachmentId);

                    return true;
                }
            }
        }

        return false;
    }
}
