<?php

namespace App\Zoho\Service\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Zoho\Entity\Desk\Ticket;
use App\Zoho\Service\ZohoDeskApiService;

class TicketService
{
    /**
     * @var ZohoDeskApiService
     */
    private $zohoDeskApiService;

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
        ZohoDeskApiService $deskApiService,
        OrganizationService $organizationService,
        AccountService $accountService,
        DepartmentService $departmentService
    ) {
        $this->zohoDeskApiService = $deskApiService;
        $this->organizationService = $organizationService;
        $this->accountService = $accountService;
        $this->departmentService = $departmentService;
    }

    public function getAllTickets(): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $organisationId = $this->zohoDeskApiService->getOrganizationId();

        return $this->zohoDeskApiService->get('tickets', $organisationId, [
            'include' => 'contacts,assignee,departments,team,isRead',
        ]);
    }

    public function getTickets(string $email): array
    {
        $accountId = $this->accountService->getAccountIdByEmail($email);

        $organisationId = $this->zohoDeskApiService->getOrganizationId();
        $result = $this->zohoDeskApiService->get('accounts/'.$accountId.'/tickets', $organisationId, [
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

        $this->zohoDeskApiService->setOrganizationId();
        $data = [
            // required
            'subject' => $ticket->getSubject(),
            'departmentId' => $ticket->getDepartmentId(),
            'contactId' => $ticket->getContactId(),
            // optional
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority(),
        ];

        $organisationId = $this->zohoDeskApiService->getOrganizationId();

        return $this->zohoDeskApiService->get('tickets', $organisationId, [], $data);
    }
}
