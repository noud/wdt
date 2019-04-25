<?php

namespace App\Form\Handler;

use App\Form\Data\AttachmentRemoveNewData;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AttachmentRemoveNewHandler
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
            /** @var AttachmentRemoveNewData $data */
            $data = $form->getData();

            $fileName = $data->filename;
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
