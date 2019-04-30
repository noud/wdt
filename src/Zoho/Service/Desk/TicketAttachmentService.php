<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\zohoApiService;

class TicketAttachmentService
{
    /**
     * @var zohoApiService
     */
    private $zohoApiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
    }

    public function getAllTicketAttachments(int $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/attachments', $this->zohoApiService->getOrganizationId());
    }

    public function getAllPublicTicketAttachments(int $ticketId): array
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

        $data = [
            'isPublic' => 'true',
            'file' => new \CURLFile($file, $fileMime, $fileName),
        ];

        return $this->zohoApiService->get('tickets/'.$ticketId.'/attachments', $organisationId, $data, true);
    }

    public function removeTicketAttachment(int $ticketId, int $attachmentId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/attachments/'.$attachmentId, $organisationId, null, false, true);
    }
}
