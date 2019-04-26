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

    /**
     * @return Ticket[]
     */
    public function getTickets(string $email): array
    {
        $accountId = $this->accountService->getAccountIdByEmail($email);

        $organisationId = $this->organizationService->getOrganizationId();
        $result = $this->zohoApiService->get('accounts/'.$accountId.'/tickets', $organisationId, [
            'include' => 'assignee,departments,team,isRead',
        ]);
        $tickets = [];
        foreach ($result['data'] as $ticketData) {
            $ticket = new Ticket();
            $ticket->setTicketNumber($ticketData['ticketNumber']);
            $ticket->setSubject($ticketData['subject']);
            $ticket->setStatus($ticketData['status']);
            $tickets[] = $ticket;
        }

        return $tickets;
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

        return $this->zohoApiService->get('tickets', $organisationId, [], $data);
    }
}
