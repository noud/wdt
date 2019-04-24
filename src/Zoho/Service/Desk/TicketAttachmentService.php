<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketAttachmentAddData;
use App\Zoho\Entity\Desk\TicketAttachment;
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
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/comments');

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
            return ($a['commentedTime'] > $b['commentedTime']) ? -1 : 1;
        });

        return $publicTicketAttachments;
    }

    public function addTicketAttachment(TicketAttachmentAddData $ticketAttachmentData, string $ticketId)
    {
        $ticketAttachment = new TicketAttachment();
        $ticketAttachment->setContent((string) $ticketAttachmentData->isPublic);

        $this->createTicketAttachment($ticketAttachment, $ticketId);
    }

    public function createTicketAttachment(?TicketAttachment $ticketAttachment, string $ticketId)
    {
        // avoid unused param
        $ticketAttachment = $ticketAttachment;

        $this->zohoDeskApiService->setOrganizationId();
        $data = [
            'isPublic' => 'true',
            'file' => new \CURLFile('/var/www/klantportaal/public/build/example33.txt', 'text/plain', 'x66.txt'),
        ];
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/attachments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId(), $data, true);
    }
}
