<?php

namespace App\Controller\Zoho;

use App\Service\PageService;
use App\Zoho\Service\Desk\AccountService;
use App\Zoho\Service\Desk\ContactService;
use App\Zoho\Service\Desk\DepartmentService;
use App\Zoho\Service\Desk\OrganizationService;
use App\Zoho\Service\ZohoDeskApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoDeskController extends AbstractController
{
    /**
     * @var ZohoDeskApiService
     */
    private $deskWebservice;

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
     * @var PageService
     */
    private $pageService;

    public function __construct(
        ZohoDeskApiService $zohoDeskService,
        OrganizationService $organizationService,
        DepartmentService $departmentService,
        ContactService $contactService,
        AccountService $accountService,
        PageService $pageService
    ) {
        $this->deskWebservice = $zohoDeskService;
        $this->organizationService = $organizationService;
        $this->departmentService = $departmentService;
        $this->contactService = $contactService;
        $this->accountService = $accountService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/desk/organizations", name="zoho_desk_organizations")
     */
    public function getDeskOrganizations()
    {
        $result = $this->organizationService->getAllOrganizations();
        dump($result);
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
    public function getDeskDepartments()
    {
        $result = $this->departmentService->getAllDepartments();
        dump($result);
        $ticketsInfo = '';
        foreach ($result['data'] as $department) {
            $ticketsInfo .= $department['id'].' '.$department['name'].'<br />';
        }

        return new Response(
            '<html><body>Departments: <br />'.$ticketsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/contacts/index", name="zoho_desk_contacts")
     */
    public function getDeskContacts()
    {
        $result = $this->contactService->getAllContacts();
        dump($result);
        $contactsInfo = '';
        foreach ($result['data'] as $contact) {
            $contactsInfo .= $contact['id'].' '.$contact['email'].'<br />';
        }

        return new Response(
            '<html><body>Contacts: <br />'.$contactsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/accounts/index", name="zoho_desk_accounts")
     */
    public function getDeskAccounts()
    {
        $result = $this->accountService->getAllAccounts();
        dump($result);
        $accountsInfo = '';
        foreach ($result['data'] as $account) {
            $accountsInfo .= $account['id'].' '.$account['accountName'].' '.$account['email'].'<br />';
            //$accountsInfo .= $account->zohoCRMAccount->id.' '.$account->id.' '.$account->email.'<br />';
        }

        return new Response(
            '<html><body>Accounts: <br />'.$accountsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/accounts/contacts/index/{accountId}", name="zoho_desk_accounts_contacts")
     */
    public function getDeskAccountContacts(string $accountId)
    {
        $result = $this->accountService->getAllAccountContacts($accountId);
        dump($result);
        $accountContactsInfo = '';
        foreach ($result['data'] as $accountContact) {
            $accountContactsInfo .= $accountContact['id'].' '.$accountContact['lastName'].' '.$accountContact['email'].'<br />';
        }

        return new Response(
            '<html><body>Account contacts: <br />'.$accountContactsInfo.'</body></html>'
            );
    }
}
