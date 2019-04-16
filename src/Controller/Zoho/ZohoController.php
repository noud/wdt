<?php

namespace App\Controller\Zoho;

use App\Entity\User;
use App\Service\Zoho\ZohoAccessTokenService;
use App\Service\Zoho\ZohoBooksApiService;
use App\Service\Zoho\ZohoCrmApiService;
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
     * @var ZohoAccessTokenService
     */
    private $zohoAccessTokenService;

    public function __construct(
        ZohoCrmApiService $zohoCrmService,
        ZohoBooksApiService $zohoBooksService,
        ZohoAccessTokenService $zohoAccessTokenService
    ) {
        $this->contactsWebservice = $zohoCrmService;
        $this->booksWebservice = $zohoBooksService;
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
}
