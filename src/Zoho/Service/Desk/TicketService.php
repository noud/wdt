<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Entity\Desk\Ticket;
use App\Zoho\Form\Data\Desk\TicketAddData;
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

    public function getAllTickets()
    {
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('tickets', [
            'include' => 'contacts,assignee,departments,team,isRead',
        ]);

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
    }

    public function getTickets(string $email)
    {
        $accountId = $this->accountService->getAccountIdByEmail($email);

        $this->zohoDeskApiService->setService('accounts/'.$accountId.'/tickets', [
            'include' => 'assignee,departments,team,isRead',
        ]);
        $result = $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());

        usort($result['data'], function ($a, $b) {
            return ($a['ticketNumber'] > $b['ticketNumber']) ? -1 : 1;
        });

        $tickets = [];
        foreach ($result['data'] as $ticketData) {
            $ticket = new Ticket();
            $ticket->setId($ticketData['id']);
            $ticket->setTicketNumber($ticketData['ticketNumber']);
            $ticket->setSubject($ticketData['subject']);
            $ticket->setStatus($ticketData['status']);
            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function getTicket(string $ticketId)
    {
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId, [
            'include' => 'contacts,products,assignee,departments,team',
        ]);
        $result = $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());

        return $result;
    }

    public function addTicket(TicketAddData $ticketData)
    {
        $ticket = new Ticket();
        $ticket->setDepartmentId($this->departmentService->getDepartmentId());
        /** @var string $contactId */
        $contactId = $this->accountService->getAccountContactIdByEmail($ticketData->email);
        $ticket->setContactId($contactId);
        $ticket->setSubject($ticketData->subject);
        $ticket->setDescription($ticketData->description);
        $ticket->setPriority($ticketData->priority);

        $this->createTicket($ticket);
    }

    public function createTicket(Ticket $ticket)
    {
        $this->zohoDeskApiService->setOrgId();
        $data = [
            // required
            'subject' => $ticket->getSubject(),
            'departmentId' => $ticket->getDepartmentId(),
            'contactId' => $ticket->getContactId(),
            // optional
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority(),
        ];

        $this->zohoDeskApiService->setService('tickets');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId(), $data);
    }
}
