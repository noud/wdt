<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\TicketComment;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;

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

    public function getAllTicketComments(string $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/comments', $organisationId);
    }

    public function getAllPublicTicketComments(string $ticketId): array
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

    public function addTicketComment(TicketCommentAddData $ticketCommentData, string $ticketId)
    {
        $ticketComment = new TicketComment();
        $ticketComment->setContent($ticketCommentData->content);

        $this->createTicketComment($ticketComment, $ticketId);
    }

    public function createTicketComment(TicketComment $ticketComment, string $ticketId)
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
