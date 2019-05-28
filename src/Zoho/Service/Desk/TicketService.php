<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\Ticket;
use App\Zoho\Enum\TicketStatusEnum;

class TicketService
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
     * @var AccountService
     */
    private $accountService;

    /**
     * @var DepartmentService
     */
    private $departmentService;

    /**
     * @var SearchService
     */
    private $searchService;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService,
        AccountService $accountService,
        DepartmentService $departmentService,
        SearchService $searchService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
        $this->accountService = $accountService;
        $this->departmentService = $departmentService;
        $this->searchService = $searchService;
    }

    public function searchTickets(string $email, string $status = null): array
    {
        $cacheKey = sprintf('zoho_desk_tickets_%s', md5($email.$status));
        $hit = $this->zohoApiService->getFromCache($cacheKey);
        if (false === $hit) {
            $accountId = $this->accountService->getAccountIdByEmail($email);
            $organisationId = $this->organizationService->getOrganizationId();

            $from = 0;
            $limit = 100;
            $totalResult = [];

            while (true) {
                $params = [
                    'from' => $from,
                    'limit' => $limit,
                    'accountId' => $accountId,
                    'sortBy' => '-createdTime',
                ];
                if ($status) {
                    $params['status'] = $status;
                }
                $result = $this->zohoApiService->get('tickets/search', $organisationId, $params);
                if (isset($result['data']) && \count($result['data'])) {
                    $totalResult = array_merge($totalResult, $result['data']);
                    $from += $limit;
                } else {
                    break;
                }
            }

            $tickets = $this->copyTickets($totalResult);
            $this->zohoApiService->saveToCache($cacheKey, $tickets);

            return $tickets;
        }

        return $hit;
    }

    private function copyTickets(array $ticketsData): array
    {
        $tickets = [];
        foreach ($ticketsData as $ticketData) {
            $ticket = new Ticket();
            $ticket->setId($ticketData['id']);
            $ticket->setTicketNumber($ticketData['ticketNumber']);
            $ticket->setSubject($ticketData['subject']);
            $ticket->setStatus($ticketData['status']);
            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function getAllTickets(): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        $from = 0;
        $limit = 99;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('tickets', $organisationId, [
                'from' => $from,
                'limit' => $limit,
                'include' => 'contacts,assignee,departments,team,isRead',
                'sortBy' => '-createdTime',
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

    /**
     * @return Ticket[]
     */
    public function getTickets(string $email): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        $from = 0;
        $limit = 100;
        $totalResult = [];

        $accountId = $this->accountService->getAccountIdByEmail($email);
        if ($accountId) {
            while (true) {
                $result = $this->zohoApiService->get('accounts/'.$accountId.'/tickets', $organisationId, [
                    'include' => 'products,assignee,departments,team,isRead',
                    'from' => $from,
                    'limit' => $limit,
                    'sortBy' => '-createdTime',
                ]);
                if (isset($result['data']) && \count($result['data'])) {
                    $totalResult = array_merge($totalResult, $result['data']);
                    $from += $limit;
                } else {
                    break;
                }
            }
        } else {
            $result = $this->searchService->search($email, 'contacts');
            if ($result) {
                $contactId = $result['data'][0]['id'];
                while (true) {
                    $result = $this->zohoApiService->get('contacts/'.$contactId.'/tickets', $organisationId, [
                            'include' => 'assignee,departments,team,isRead',
                            'from' => $from,
                            'limit' => $limit,
                            'sortBy' => '-createdTime',
                        ]);
                    if (isset($result['data']) && \count($result['data'])) {
                        $totalResult = array_merge($totalResult, $result['data']);
                        $from += $limit;
                    } else {
                        break;
                    }
                }
            } else {
                $result = [];
            }
        }

        return $this->copyTickets($totalResult);
    }

    public function getTicket(int $ticketId): array
    {
        $cacheKey = sprintf('zoho_desk_ticket_%s', md5((string) $ticketId));
        $hit = $this->zohoApiService->getFromCache($cacheKey);
        if (false === $hit) {
            $organisationId = $this->organizationService->getOrganizationId();

            $ticket = $this->zohoApiService->get('tickets/'.$ticketId, $organisationId, [
                'include' => 'contacts,products,assignee,departments,team',
            ]);
            $this->zohoApiService->saveToCache($cacheKey, $ticket);

            return $ticket;
        }

        return $hit;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function addTicket(TicketAddData $ticketData, string $email): ?array
    {
        // delete the old tickets caches..
        $cacheKey = sprintf('zoho_desk_tickets_%s', md5($email.TicketStatusEnum::OPEN));
        $this->zohoApiService->deleteCacheByKey($cacheKey);
        $cacheKey = sprintf('zoho_desk_tickets_%s', md5($email.null));
        $this->zohoApiService->deleteCacheByKey($cacheKey);

        $ticket = new Ticket();
        $ticket->setDepartmentId($this->departmentService->getDepartmentId());
        /** @var string $contactId */
        $contactId = $this->accountService->getAccountContactIdByEmail($ticketData->email);
        $ticket->setContactId($contactId);
        $ticket->setSubject($ticketData->subject);
        $ticket->setDescription($ticketData->description);
        $ticket->setPriority($ticketData->priority);

        $data = [
            // required
            'subject' => $ticket->getSubject(),
            'departmentId' => $ticket->getDepartmentId(),
            'contactId' => $ticket->getContactId(),
            // optional
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority(),
        ];

        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->post('tickets', $organisationId, [], json_encode($data));
    }
}
