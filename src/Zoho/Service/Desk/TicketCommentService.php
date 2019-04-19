<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Entity\Desk\TicketComment;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;
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

    public function getAllTicketComments(string $ticketId): array
    {
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/comments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
    }

    public function getAllPublicTicketComments(string $ticketId): array
    {
        $ticketComments = $this->getAllTicketComments($ticketId);
        $filterBy = true;
        $publicTicketComments = array_filter($ticketComments['data'], function ($var) use ($filterBy) {
            return $var['isPublic'] === $filterBy;
        });

        return $publicTicketComments;
    }

    public function addTicketComment(TicketCommentAddData $ticketCommentData, string $ticketId)
    {
        $ticketComment = new TicketComment();
        $ticketComment->setContent($ticketCommentData->content);

        $this->createTicketComment($ticketComment, $ticketId);
    }

    public function createTicketComment(TicketComment $ticketComment, string $ticketId)
    {
        $this->zohoDeskApiService->setOrgId();
        $data = [
            'isPublic' => 'true',
            'content' => $ticketComment->getContent(),
            'contentType' => 'html',
        ];

        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/comments');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId(), $data);
    }
}
