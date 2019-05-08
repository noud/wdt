<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\Ticket;

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
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService,
        AccountService $accountService,
        DepartmentService $departmentService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
        $this->accountService = $accountService;
        $this->departmentService = $departmentService;
    }

    public function getAllTickets(): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets', $organisationId, [
            'include' => 'contacts,assignee,departments,team,isRead',
        ]);
    }

    private function sortTicketsByNumber(array $tickets): array
    {
        usort($tickets, function ($a, $b) {
            return $b['ticketNumber'] <=> $a['ticketNumber'];
        });

        return $tickets;
    }

    /**
     * @return Ticket[]
     */
    public function getTickets(string $email): array
    {
        $accountId = $this->accountService->getAccountIdByEmail($email);

        $organisationId = $this->organizationService->getOrganizationId();
        $result = $this->zohoApiService->get('accounts/'.$accountId.'/tickets', $organisationId, [
            'include' => 'products,assignee,departments,team,isRead',
            'limit' => '100',
        ]);

        $resultSorted = $this->sortTicketsByNumber($result['data']);

        $tickets = [];
        foreach ($resultSorted as $ticketData) {
            $ticket = new Ticket();
            $ticket->setId($ticketData['id']);
            $ticket->setTicketNumber($ticketData['ticketNumber']);
            $ticket->setSubject($ticketData['subject']);
            $ticket->setStatus($ticketData['status']);
            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function getTicket(int $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId, $organisationId, [
            'include' => 'contacts,products,assignee,departments,team',
        ]);
    }

    public function addTicket(TicketAddData $ticketData): ?array
    {
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

        return $this->zohoApiService->get('tickets', $organisationId, [], json_encode($data));
    }
}
