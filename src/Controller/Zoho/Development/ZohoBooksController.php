<?php

namespace App\Controller\Zoho\Development;

use App\Zoho\Service\ZohoBooksApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoBooksController extends AbstractController
{
    /**
     * @var ZohoBooksApiService
     */
    private $booksWebservice;

    public function __construct(
        ZohoBooksApiService $zohoBooksService
    ) {
        $this->booksWebservice = $zohoBooksService;
    }

    /**
     * @Route("/books/organizations", name="zoho_books_organizations")
     */
    public function getOrganizations(): Response
    {
        /** @var \stdClass $result */
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
    public function getContacts(): Response
    {
        /** @var \stdClass $result */
        $result = $this->booksWebservice->getContacts();
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
    public function getInvoices(): Response
    {
        /** @var \stdClass $result */
        $result = $this->booksWebservice->getInvoices();
        $invoicesInfo = '';
        foreach ($result->invoices as $invoice) {
            $invoicesInfo .= $invoice->invoice_id.' '.$invoice->total.'<br />';
        }

        return new Response(
            '<html><body>Invoices: '.$result->code.' '.$result->message.'<br />'.$invoicesInfo.'</body></html>'
        );
    }
}
