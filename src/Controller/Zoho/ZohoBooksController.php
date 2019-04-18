<?php

namespace App\Controller\Zoho;

use App\Zoho\Service\Books\ContactService;
use App\Zoho\Service\Books\InvoiceService;
use App\Zoho\Service\Books\OrganizationService;
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

    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    public function __construct(
        ZohoBooksApiService $zohoBooksService,
        ContactService $contactService,
        InvoiceService $invoiceService,
        OrganizationService $organizationService
    ) {
        $this->booksWebservice = $zohoBooksService;
        $this->contactService = $contactService;
        $this->invoiceService = $invoiceService;
        $this->organizationService = $organizationService;
    }

    /**
     * @Route("/books/organizations", name="zoho_books_organizations")
     */
    public function getOrganizations()
    {
        $result = $this->organizationService->getAllOrganizations();
        dump($result);

        return new Response(
            '<html><body>Organizations: '.$result['code'].' '.$result['message'].'<br />'
            .$result['organizations'][0]['organization_id'].
            '</body></html>'
        );
    }

    /**
     * @Route("/books/contacts", name="zoho_books_contacts")
     */
    public function getContacts()
    {
        $result = $this->contactService->getAllContacts();
        dump($result);
        $contactNames = '';
        foreach ($result['contacts'] as $contact) {
            $contactNames .= $contact['contact_name'].'<br />';
        }

        return new Response(
            '<html><body>Contacts: '.$result['code'].' '.$result['message'].'<br />'.$contactNames.'</body></html>'
        );
    }
}
