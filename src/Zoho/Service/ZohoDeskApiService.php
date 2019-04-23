<?php

namespace App\Zoho\Service;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\Ticket;
use App\Zoho\Form\Data\Desk\TicketAddData;

class ZohoDeskApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var int
     */
    private $organizationId;

    public function __construct(
        ZohoApiService $zohoDeskApiService
    ) {
        $this->apiService = $zohoDeskApiService;
    }

    public function getOrganizations(): \stdClass
    {
        return $this->apiService->getRequest('organizations', 0);
    }

    public function getOrganizationId(): int
    {
        $organizations = $this->getOrganizations();

        return (int) $organizations->data[0]->id;
    }

    public function getTicketsAll(): \stdClass
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('tickets?include=contacts,assignee,departments,team,isRead', $this->organizationId);
    }

    public function getTickets(string $email): array
    {
        $this->organizationId = $this->getOrganizationId();
        $accountId = $this->getAccountIdByEmail($email);

        $result = $this->apiService->getRequest('accounts/'.$accountId.'/tickets?include=assignee,departments,team,isRead', $this->organizationId);
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

    public function getDepartments(): \stdClass
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('departments?isEnabled=true&chatStatus=AVAILABLE', $this->organizationId);
    }

    public function getDepartmentId(): int
    {
        $departments = $this->getDepartments();

        return (int) (null !== $departments->data) ? $departments->data[0]->id : '';
    }

    public function getContacts(): \stdClass
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('contacts?include=accounts', $this->organizationId);
    }

    public function getAccounts(): \stdClass
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('accounts', $this->organizationId);
    }

    public function getAccountContacts(string $accountId): \stdClass
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('accounts/'.$accountId.'/contacts', $this->organizationId);
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
    }

    public function addTicket(TicketAddData $ticketData): \stdClass
    {
        $ticket = new Ticket();
        $ticket->setDepartmentId($this->getDepartmentId());
        /** @var string $contactId */
        $contactId = $this->getAccountContactIdByEmail($ticketData->email);
        $ticket->setContactId($contactId);
        $ticket->setSubject($ticketData->subject);
        $ticket->setDescription($ticketData->description);
        $ticket->setPriority($ticketData->priority);

        $this->organizationId = $this->getOrganizationId();
        $data = [
            // required
            'subject' => $ticket->getSubject(),
            'departmentId' => $ticket->getDepartmentId(),
            'contactId' => $ticket->getContactId(),
            // optional
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority(),
        ];

        return $this->apiService->getRequest('tickets', $this->organizationId, $data);
    }
}
