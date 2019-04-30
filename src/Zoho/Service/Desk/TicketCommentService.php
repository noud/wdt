<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\TicketComment;

class TicketCommentService
{
    /**
     * @var ZohoApiService
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

    public function getAllTicketComments(int $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/comments', $organisationId);
    }

    private function sortTicketCommentsByDate(array $ticketComments): array
    {
        usort($ticketComments, function ($a, $b) {
            return $b['commentedTime'] <=> $a['commentedTime'];
        });

        return $ticketComments;
    }

    public function getAllPublicTicketComments(int $ticketId): array
    {
        $ticketComments = $this->getAllTicketComments($ticketId);
        $publicTicketComments = array_filter($ticketComments['data'], function ($comment) {
            return $comment['isPublic'];
        });

        $publicTicketComments = $this->sortTicketCommentsByDate($publicTicketComments);

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
        $data = [
            'isPublic' => 'true',
            'content' => $ticketComment->getContent(),
            'contentType' => 'html',
        ];

        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/comments', $organisationId, [], $data);
    }
}
