<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Entity\Desk\TicketAttachment;
use App\Zoho\Form\Data\Desk\TicketAttachmentAddData;
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
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/comments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
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
        $ticketAttachment->setContent($ticketAttachmentData->content);

        $this->createTicketAttachment($ticketAttachment, $ticketId);
    }

    public function createTicketAttachment(?TicketAttachment $ticketAttachment, string $ticketId)
    {
        $this->zohoDeskApiService->setOrgId();
        $data = [
            'isPublic' => 'true',
        ];
        $files = [
            'file' => '@' . realpath('/var/www/klantportaal/config/zoho/example.txt')
        ];
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/attachment');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId(), $data, $files);
    }
}
