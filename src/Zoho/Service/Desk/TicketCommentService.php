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

        $from = 0;
        $limit = 200000;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('tickets/'.$ticketId.'/comments', $organisationId, [
                'from' => $from,
                'limit' => $limit,
                'sortBy' => '-commentedTime',
            ]);
            if (isset($result['data']) && \count($result['data'])) {
                $totalResult = array_merge($totalResult, $result['data']);
                $from += $limit;
            } else {
                break;
            }
        }

        return $totalResult;
    }

    public function getAllPublicTicketComments(int $ticketId): array
    {
        $ticketComments = $this->getAllTicketComments($ticketId);
        $publicTicketComments = array_filter($ticketComments, function ($comment) {
            return $comment['isPublic'];
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
        $data = [
            'isPublic' => 'true',
            'content' => $ticketComment->getContent(),
            'contentType' => 'html',
        ];

        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->post('tickets/'.$ticketId.'/comments', $organisationId, [], json_encode($data));
    }
}
