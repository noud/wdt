<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Entity\Desk\TicketComment;
use App\Zoho\Service\ZohoDeskApiService;

class TicketCommentService
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

    public function getAllTicketComments(int $ticketId): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/comments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId());
    }

    public function getAllPublicTicketComments(int $ticketId): array
    {
        $ticketComments = $this->getAllTicketComments($ticketId);
        $filterBy = true;
        $publicTicketComments = array_filter($ticketComments['data'], function ($var) use ($filterBy) {
            return $var['isPublic'] === $filterBy;
        });

        usort($publicTicketComments, function ($a, $b) {
            return ($a['commentedTime'] > $b['commentedTime']) ? -1 : 1;
        });

        return $publicTicketComments;
    }

    public function addTicketComment(TicketCommentAddData $ticketCommentData, int $ticketId)
    {
        $ticketComment = new TicketComment();
        $ticketComment->setContent($ticketCommentData->content);

        $this->createTicketComment($ticketComment, $ticketId);
    }

    public function createTicketComment(TicketComment $ticketComment, int $ticketId)
    {
        $this->zohoDeskApiService->setOrganizationId();
        $data = [
            'isPublic' => 'true',
            'content' => $ticketComment->getContent(),
            'contentType' => 'html',
        ];

        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/comments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId(), $data);
    }
}
