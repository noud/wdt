<?php

namespace App\Controller\Zoho;

use App\Entity\User;
use App\Form\Data\TicketAddData;
use App\Form\Handler\TicketAddHandler;
use App\Form\Type\TicketAddType;
use App\Service\PageService;
use App\Service\Zoho\ZohoAccessTokenService;
use App\Service\Zoho\ZohoBooksApiService;
use App\Service\Zoho\ZohoCrmApiService;
use App\Service\Zoho\ZohoDeskApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoController extends AbstractController
{
    /**
     * @var ZohoCrmApiService
     */
    private $contactsWebservice;

    /**
     * @var ZohoBooksApiService
     */
    private $booksWebservice;

    /**
     * @var ZohoDeskApiService
     */
    private $deskWebservice;

    /**
     * @var ZohoAccessTokenService
     */
    private $zohoAccessTokenService;

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        ZohoCrmApiService $zohoCrmService,
        ZohoBooksApiService $zohoBooksService,
        ZohoDeskApiService $zohoDeskService,
        ZohoAccessTokenService $zohoAccessTokenService,
        PageService $pageService
    ) {
        $this->contactsWebservice = $zohoCrmService;
        $this->booksWebservice = $zohoBooksService;
        $this->deskWebservice = $zohoDeskService;
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/zoho-has-access-to-portal/{email}", name="zoho_has_access_to_portal")
     */
    public function hasAccessToPortal(User $user): Response
    {
        $access = $this->contactsWebservice->hasAccessToPortal($user);

        return new Response(
            '<html><body>'.$access.'</body></html>'
        );
    }

    /**
     * @Route("/crm/contact/get-id/{email}", name="zoho_crm_contact_id")
     */
    public function getCrmContactId(User $user): Response
    {
        /** @var string $email */
        $email = $user->getEmail();
        $id = $this->contactsWebservice->getContactIdByEmail($email);

        return new Response(
            '<html><body>Id: '.$id.'</body></html>'
            );
    }

    /**
     * @Route("/generate-access-token/{grantToken}", name="zoho_generate_access_token")
     */
    public function generateAccessToken(string $grantToken)
    {
        $this->zohoAccessTokenService->generateAccessToken($grantToken);

        return new Response(
            '<html><body>Grant Token gegenereerd.</body></html>'
        );
    }

    /**
     * @Route("/books/organizations", name="zoho_books_organizations")
     */
    public function getOrganizations()
    {
        $result = $this->booksWebservice->getOrganizations();

        return new Response(
            '<html><body>Organizations: '.$result->code.' '.$result->message.'<br />'
            .$result->organizations[0]->organization_id.
            '</body></html>'
        );
    }

    /**
     * @Route("/books/contacts", name="zoho_books_contacts")
     */
    public function getContacts()
    {
        $result = $this->booksWebservice->getContacts();
        dump($result);
        $contactNames = '';
        foreach ($result->contacts as $contact) {
            $contactNames .= $contact->contact_name.'<br />';
        }

        return new Response(
            '<html><body>Contacts: '.$result->code.' '.$result->message.'<br />'.$contactNames.'</body></html>'
        );
    }

    /**
     * @Route("/books/invoices", name="zoho_books_invoices")
     */
    public function getInvoices()
    {
        $result = $this->booksWebservice->getInvoices();
        dump($result);
        $invoicesInfo = '';
        foreach ($result->invoices as $invoice) {
            $invoicesInfo .= $invoice->invoice_id.' '.$invoice->total.'<br />';
        }

        return new Response(
            '<html><body>Invoices: '.$result->code.' '.$result->message.'<br />'.$invoicesInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/tickets/all", name="zoho_desk_tickets_all")
     */
    public function getDeskTicketsAll()
    {
        $result = $this->deskWebservice->getTicketsAll();
        dump($result);
        $ticketsInfo = '';
        foreach ($result->data as $ticket) {
            $ticketsInfo .= $ticket->ticketNumber.' '.$ticket->subject.'<br />';
        }

        return new Response(
            '<html><body>Tickets: <br />'.$ticketsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/tickets/index", name="zoho_desk_tickets")
     */
    public function getDeskTickets()
    {
        $user = $this->getUser();
        /** @var string $email */
        $email = $user->getEmail();
        $tickets = $this->deskWebservice->getTickets($email);

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * @Route("/desk/organizations", name="zoho_desk_organizations")
     */
    public function getDeskOrganizations()
    {
        $result = $this->deskWebservice->getOrganizations();
        dump($result);
        $organizationsInfo = '';
        foreach ($result->data as $organization) {
            $organizationsInfo .= $organization->id.' '.$organization->companyName.'<br />';
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
        $result = $this->deskWebservice->getDepartments();
        dump($result);
        $ticketsInfo = '';
        foreach ($result->data as $department) {
            $ticketsInfo .= $department->id.' '.$department->name.'<br />';
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
        $result = $this->deskWebservice->getContacts();
        dump($result);
        $contactsInfo = '';
        foreach ($result->data as $contact) {
            $contactsInfo .= $contact->id.' '.$contact->email.'<br />';
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
        $result = $this->deskWebservice->getAccounts();
        dump($result);
        $accountsInfo = '';
        foreach ($result->data as $account) {
            $accountsInfo .= $account->id.' '.$account->accountName.' '.$account->email.'<br />';
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
        $result = $this->deskWebservice->getAccountContacts($accountId);
        dump($result);
        $accountContactsInfo = '';
        foreach ($result->data as $accountContact) {
            $accountContactsInfo .= $accountContact->id.' '.$accountContact->lastName.' '.$accountContact->email.'<br />';
        }

        return new Response(
            '<html><body>Account contacts: <br />'.$accountContactsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/tickets/create", name="zoho_desk_tickets_create")
     */
    public function createDeskTicket(TicketAddHandler $ticketAddHandler, Request $request): Response
    {
        $user = $this->getUser();
        $data = new TicketAddData($user);
        $form = $this->createForm(TicketAddType::class, $data);

        if ($ticketAddHandler->handleRequest($form, $request)) {
            $this->addFlash('success', 'Ticket is toegevoegd.');

            return $this->redirectToRoute('zoho_desk_tickets_create_thanks');
        }

        return $this->render('ticket/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/desk/tickets/create-thanks", name="zoho_desk_tickets_create_thanks")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addThanks(Request $request): Response
    {
        return $this->render('ticket/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }
}
