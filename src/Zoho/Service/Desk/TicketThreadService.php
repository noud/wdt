<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\TicketThread;

class TicketThreadService
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
     * @var DepartmentService
     */
    private $departmentService;

    /**
     * @var SupportEmailAddressService
     */
    private $supportEmailAddressService;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService,
        DepartmentService $departmentService,
        SupportEmailAddressService $supportEmailAddressService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
        $this->departmentService = $departmentService;
        $this->supportEmailAddressService = $supportEmailAddressService;
    }

    public function getAllTicketThreads(int $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/threads', $organisationId);
    }

    public function getTicketThread(int $ticketId, int $threadId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/threads/'.$threadId, $organisationId, [
            'include' => 'plainText',
        ]);
    }

    private function sortTicketThreadsByDate(array $ticketThreads): array
    {
        usort($ticketThreads, function ($a, $b) {
            return $b['createdTime'] <=> $a['createdTime'];
        });

        return $ticketThreads;
    }

    public function getAllPublicTicketThreads(int $ticketId): array
    {
        $ticketThreads = $this->getAllTicketThreads($ticketId);
        $publicTicketThreads = array_filter($ticketThreads['data'], function ($comment) {
            return 'public' === $comment['visibility'];
        });

        $publicTicketThreads = $this->sortTicketThreadsByDate($publicTicketThreads);

        $ticketThreads = [];
        foreach ($publicTicketThreads as $publicTicketThread) {
            $ticketThread = $this->getTicketThread($ticketId, $publicTicketThread['id']);
            $ticketThreads[] = [
                'content' => $ticketThread['content'],
                'fromEmailAddress' => isset($ticketThread['fromEmailAddress']) ? $ticketThread['fromEmailAddress'] : '',
            ];
        }

        return $ticketThreads;
    }

    public function addTicketThread(TicketCommentAddData $ticketThreadData, int $ticketId, string $email)
    {
        $ticketThread = new TicketThread();
        $ticketThread->setContent($ticketThreadData->content);

        $this->createTicketThread($ticketThread, $ticketId, $email);
    }

    public function createTicketThread(TicketThread $ticketThread, int $ticketId, string $email)
    {
        $to = $this->supportEmailAddressService->getFirstSupportEmailAddress();

        $data = [
            'channel' => 'EMAIL',
            'content' => $ticketThread->getContent(),
            'contentType' => 'html',
            'fromEmailAddress' => $email,
            'to' => $to,
            'isPrivate' => 'false',
            'isForward' => 'true',
        ];

        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->post('tickets/'.$ticketId.'/sendReply', $organisationId, [], $data);
    }
}
