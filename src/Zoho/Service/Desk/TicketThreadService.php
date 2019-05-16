<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\TicketThread;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;

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

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
    }

    public function getAllTicketThreads(string $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/threads', $organisationId);
    }
    
    public function getTicketThread(string $ticketId, string $threadId): array
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

    public function getAllPublicTicketThreads(string $ticketId): array
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

    public function addTicketThread(TicketCommentAddData $ticketThreadData, string $ticketId)
    {
        $ticketThread = new TicketThread();
        $ticketThread->setContent($ticketThreadData->content);

        $this->createTicketThread($ticketThread, $ticketId);
    }

    public function createTicketThread(TicketThread $ticketThread, string $ticketId)
    {
        $email = 'test@test.nl';   // @TODO get from Security
        $email = 'support@webdesigntilburg.nl';
        $to = 'support@wdtinternetbv.zohodesk.eu';   // @TODO get from API
        $data = [
            'channel' => 'EMAIL',
            'content' => $ticketThread->getContent(),
            'contentType' => 'html',
            'fromEmailAddress' => $email,
            'to' => $to,
        ];

        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/sendReply', $organisationId, [], $data);
    }
}
