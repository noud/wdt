<?php

namespace App\Zoho\Service\Books;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Books\Invoice;

class InvoiceService
{
    /**
     * @var ZohoApiService
     */
    private $zohoBooksApiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * InvoiceService constructor.
     */
    public function __construct(
        ZohoApiService $zohoBooksApiService,
        OrganizationService $organizationService
    ) {
        $this->zohoBooksApiService = $zohoBooksApiService;
        $this->organizationService = $organizationService;
    }

    public function getAllInvoices()
    {
        $this->zohoBooksApiService->setService('invoices', [
            'organization_id' => $this->organizationService->getOrganizationId(),
        ]);
        $invoices = [];
        $responseData = $this->zohoBooksApiService->getRequest();
        if (isset($responseData['invoices'])) {
            foreach ($responseData['invoices'] as $invoice) {
                $invoice = new Invoice($invoice);
                $invoices[] = $invoice;
            }
        }

        return $invoices;
    }
}
