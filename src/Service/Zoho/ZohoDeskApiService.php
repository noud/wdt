<?php

namespace App\Service\Zoho;

use App\Form\Data\TicketAddData;
use App\Service\Zoho\Entity\Ticket;

class ZohoDeskApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var string
     */
    private $orgId;

    public function __construct(
        ZohoApiService $zohoDeskApiService
    ) {
        $this->apiService = $zohoDeskApiService;
    }

    public function getOrganizations()
    {
        $url = $this->apiService->apiBaseUrl.'organizations';

        return $this->apiService->getRequest($url, true);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->data[0]->id;
    }

    public function getTicketsAll()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'tickets?include=contacts,assignee,departments,team,isRead';

        return $this->apiService->getRequest($url, $this->orgId);
    }

    public function getTickets(string $email)
    {
        $this->orgId = $this->getOrganizationId();
        $accountId = $this->getAccountIdByEmail($email);
        $url = $this->apiService->apiBaseUrl.'accounts/'.$accountId.'/tickets?include=assignee,departments,team,isRead';

        $result = $this->apiService->getRequest($url, $this->orgId);
        $tickets = [];
        foreach ($result->data as $ticketData) {
            $ticket = new Ticket();
            $ticket->setTicketNumber($ticketData->ticketNumber);
            $ticket->setSubject($ticketData->subject);
            $ticket->setStatus($ticketData->status);
            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function getDepartments()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'departments?isEnabled=true&chatStatus=AVAILABLE';

        return $this->apiService->getRequest($url, $this->orgId);
    }

    public function getDepartmentId()
    {
        $departments = $this->getDepartments();

        return $departments->data[0]->id;
    }

    public function getContacts()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'contacts?include=accounts';

        return $this->apiService->getRequest($url, $this->orgId);
    }

    public function getAccounts()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'accounts';

        return $this->apiService->getRequest($url, $this->orgId);
    }

    public function getAccountContacts(string $accountId)
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'accounts/'.$accountId.'/contacts';

        return $this->apiService->getRequest($url, $this->orgId);
    }

    public function getAccountContactIdByEmail(string $email): ?string
    {
        $accounts = $this->getAccounts();
        foreach ($accounts->data as $account) {
            $accountContacts = $this->getAccountContacts($account->id);
            foreach ($accountContacts->data as $contact) {
                if ($contact->email === $email) {
                    return $contact->id;
                }
            }
        }

        return null;
    }

    public function getAccountIdByEmail(string $email): ?string
    {
        $accounts = $this->getAccounts();
        foreach ($accounts->data as $account) {
            $accountContacts = $this->getAccountContacts($account->id);
            foreach ($accountContacts->data as $contact) {
                if ($contact->email === $email) {
                    return $account->id;
                }
            }
        }

        return null;
    }

    public function addTicket(TicketAddData $ticketData)
    {
        $ticket = new Ticket();
        $ticket->setDepartmentId($this->getDepartmentId());
        /** @var string $contactId */
        $contactId = $this->getAccountContactIdByEmail($ticketData->email);
        $ticket->setContactId($contactId);
        $ticket->setSubject($ticketData->subject);
        $ticket->setDescription($ticketData->description);
        $ticket->setPriority($ticketData->priority);

        $this->createTicket($ticket);
    }

    public function createTicket(Ticket $ticket)
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'tickets';
        $data = [
            // required
            'subject' => $ticket->getSubject(),
            'departmentId' => $ticket->getDepartmentId(),
            'contactId' => $ticket->getContactId(),
            // optional
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority(),
        ];

        return $this->apiService->getRequest($url, $this->orgId, $data);
    }
}
