<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

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

        return $this->zohoApiService->get('tickets/'.$ticketId.'/attachments', $organisationId, [
            'sortBy' => '-createdTime',
        ]);
    }

    public function getAllPublicTicketAttachments(int $ticketId): array
    {
        $ticketAttachments = $this->getAllTicketAttachments($ticketId);
        if (!$ticketAttachments) {
            return [];
        }
        $publicTicketAttachments = array_filter($ticketAttachments['data'], function ($var) {
            return $var['isPublic'];
        });

        return $publicTicketAttachments;
    }

    public function createTicketAttachment(string $file, int $ticketId, string $fileName = null): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        /** @var string $fileMime */
        $fileMime = mime_content_type($file);
        if (!$fileName) {
            $fileName = basename($file);
        }

        $data = [
            'isPublic' => 'true',
            'file' => new \CURLFile($file, $fileMime, $fileName),
        ];

        return $this->zohoApiService->get('tickets/'.$ticketId.'/attachments', $organisationId, [], $data);
    }

    public function removeTicketAttachment(int $ticketId, int $attachmentId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/attachments/'.$attachmentId, $organisationId, [], null, true);
    }
}
