<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Service\ZohoDeskApiService;

class TicketAttachmentService
{
    /**
     * @var ZohoDeskApiService
     */
    private $zohoDeskApiService;

    /**
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoDeskApiService $deskApiService
    ) {
        $this->zohoDeskApiService = $deskApiService;
    }

    public function getAllTicketAttachments(string $ticketId): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/attachments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId());
    }

    public function getAllPublicTicketAttachments(string $ticketId): array
    {
        $ticketAttachments = $this->getAllTicketAttachments($ticketId);
        $filterBy = true;
        $publicTicketAttachments = array_filter($ticketAttachments['data'], function ($var) use ($filterBy) {
            return $var['isPublic'] === $filterBy;
        });

        usort($publicTicketAttachments, function ($a, $b) {
            return ($a['createdTime'] > $b['createdTime']) ? -1 : 1;
        });

        return $publicTicketAttachments;
    }

    public function createTicketAttachment(string $file, int $ticketId): array
    {
        /** @var string $fileMime */
        $fileMime = mime_content_type($file);
        $fileName = basename($file);

        $this->zohoDeskApiService->setOrganizationId();
        $data = [
            'isPublic' => 'true',
            'file' => new \CURLFile($file, $fileMime, $fileName),
        ];
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/attachments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId(), $data, true);
    }

    public function removeTicketAttachment(int $ticketId, int $attachmentId): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/attachments/'.$attachmentId);

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId(), null, false, true);
    }

    public function removeTicketNewAttachment(int $attachmentId): array
    {
        // for pipeline
        $attachmentId = $attachmentId;

        return [];
    }
}
