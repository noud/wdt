<?php

namespace App\Controller\Zoho;

use App\Entity\User;
use App\Service\Zoho\ZohoAccessTokenService;
use App\Service\Zoho\ZohoBooksApiService;
use App\Service\Zoho\ZohoCrmApiService;
use App\Service\Zoho\ZohoDeskApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(
        ZohoCrmApiService $zohoCrmService,
        ZohoBooksApiService $zohoBooksService,
        ZohoDeskApiService $zohoDeskService,
        ZohoAccessTokenService $zohoAccessTokenService
    ) {
        $this->contactsWebservice = $zohoCrmService;
        $this->booksWebservice = $zohoBooksService;
        $this->deskWebservice = $zohoDeskService;
        $this->zohoAccessTokenService = $zohoAccessTokenService;
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
     * @Route("/desk/tickets", name="zoho_desk_tickets")
     */
    public function getDeskTickets()
    {
        $result = $this->deskWebservice->getTickets();
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
     * @Route("/desk/contacts", name="zoho_desk_contacts")
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
     * @Route("/desk/ticket/create", name="zoho_desk_tickets_create")
     */
    public function createDeskTicket()
    {
        $result = $this->deskWebservice->createTicket();
        dump($result);
        
        return new Response(
            '<html><body>Ticket created</body></html>'
            );
    }
}
