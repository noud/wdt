<?php

namespace App\Service\Zoho;

class ZohoBooksApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var string
     */
    private $organizationId;

    public function __construct(ZohoApiService $zohoBooksApiService)
    {
        $this->apiService = $zohoBooksApiService;
    }

    public function getOrganizations()
    {
        return $this->apiService->getRequest('organizations');
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->organizations[0]->organization_id;
    }

    public function getContacts()
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('contacts?organization_id='.$this->organizationId);
    }

    public function getInvoices()
    {
        $this->organizationId = $this->getOrganizationId();

        return $this->apiService->getRequest('invoices?organization_id='.$this->organizationId);
    }
}
