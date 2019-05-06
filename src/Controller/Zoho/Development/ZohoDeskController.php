<?php

namespace App\Controller\Zoho\Development;

use App\Service\PageService;
use App\Zoho\Service\Desk\AccountService;
use App\Zoho\Service\Desk\ContactService;
use App\Zoho\Service\Desk\DepartmentService;
use App\Zoho\Service\Desk\OrganizationService;
use App\Zoho\Service\Desk\TicketResolutionHistoryService;
use App\Zoho\Service\Desk\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoDeskController extends AbstractController
{
    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var DepartmentService
     */
    private $departmentService;

    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * @var TicketResolutionHistoryService
     */
    private $ticketResolutionHistoryService;

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        OrganizationService $organizationService,
        DepartmentService $departmentService,
        ContactService $contactService,
        AccountService $accountService,
        TicketService $ticketService,
        TicketResolutionHistoryService $ticketResolutionHistoryService,
        PageService $pageService
    ) {
        $this->organizationService = $organizationService;
        $this->departmentService = $departmentService;
        $this->contactService = $contactService;
        $this->accountService = $accountService;
        $this->ticketService = $ticketService;
        $this->ticketResolutionHistoryService = $ticketResolutionHistoryService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/desk/organizations", name="zoho_desk_organizations")
     */
    public function getDeskOrganizations(): Response
    {
        $result = $this->organizationService->getAllOrganizations();
        $organizationsInfo = '';
        foreach ($result['data'] as $organization) {
            $organizationsInfo .= $organization['id'].' '.$organization['companyName'].'<br />';
        }

        return new Response(
            '<html><body>Organizations: <br />'.$organizationsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/departments", name="zoho_desk_departments")
     */
    public function getDeskDepartments(): Response
    {
        $result = $this->departmentService->getAllDepartments();
        $ticketsInfo = '';
        foreach ($result as $department) {
            $ticketsInfo .= $department['id'].' '.$department['name'].'<br />';
        }

        return new Response(
            '<html><body>Departments: <br />'.$ticketsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/contacts/index", name="zoho_desk_contacts")
     */
    public function getDeskContacts(): Response
    {
        $result = $this->contactService->getAllContacts();
        $contactsInfo = '';
        foreach ($result as $contact) {
            $contactsInfo .= $contact['id'].' '.$contact['email'].'<br />';
        }

        return new Response(
            '<html><body>Contacts: <br />'.$contactsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/accounts/index", name="zoho_desk_accounts")
     */
    public function getDeskAccounts(): Response
    {
        $result = $this->accountService->getAllAccounts();
        $accountsInfo = '';
        foreach ($result as $account) {
            $accountsInfo .= $account['id'].' '.$account['accountName'].' '.$account['email'].'<br />';
        }

        return new Response(
            '<html><body>Accounts: <br />'.$accountsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/accounts/contacts/index/{accountId}", name="zoho_desk_accounts_contacts")
     */
    public function getDeskAccountContacts(string $accountId): Response
    {
        $result = $this->accountService->getAllAccountContacts($accountId);
        $accountContactsInfo = '';
        foreach ($result as $accountContact) {
            $accountContactsInfo .= $accountContact['id'].' '.$accountContact['lastName'].' '.$accountContact['email'].'<br />';
        }

        return new Response(
            '<html><body>Account contacts: <br />'.$accountContactsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/tickets/all", name="zoho_desk_tickets_all")
     */
    public function getDeskTicketsAll(): Response
    {
        $result = $this->ticketService->getAllTickets();
        $ticketsInfo = '';
        foreach ($result as $ticket) {
            $ticketsInfo .= $ticket['id'].' '.$ticket['ticketNumber'].' '.$ticket['subject'].'<br />';
        }

        return new Response(
            '<html><body>Tickets: <br />'.$ticketsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/tickets/resolution-history/{ticketId}", name="zoho_desk_ticket_resolution_history")
     */
    public function getDeskTicketResolutionHistory(int $ticketId): Response
    {
        $result = $this->ticketResolutionHistoryService->getAllTicketResolutionHistory($ticketId);
        $ticketResolutionHistories = '';
        foreach ($result as $ticketResolutionHistory) {
            $ticketResolutionHistories .= $ticketResolutionHistory['content'].'<br />';
        }

        return new Response(
            '<html><body>Resolution history: <br />'.$ticketResolutionHistories.'</body></html>'
        );
    }
}
