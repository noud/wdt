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
        $url = $this->apiService->getApiBaseUrl().'organizations';

        return $this->apiService->getRequest($url);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->organizations[0]->organization_id;
    }

    public function getContacts()
    {
        $this->organizationId = $this->getOrganizationId();
        $url = $this->apiService->getApiBaseUrl().'contacts?organization_id='.$this->organizationId;

        return $this->apiService->getRequest($url);
    }

    public function getInvoices()
    {
        $this->organizationId = $this->getOrganizationId();
        $url = $this->apiService->getApiBaseUrl().'invoices?organization_id='.$this->organizationId;

        return $this->apiService->getRequest($url);
    }
}
