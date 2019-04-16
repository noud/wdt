<?php

namespace App\Service\Zoho;

class ZohoDeskApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var string
     */
    private $orgId;

    public function __construct(ZohoApiService $zohoDeskApiService)
    {
        $this->apiService = $zohoDeskApiService;
    }

    public function getOrganizations()
    {
        $url = $this->apiService->apiBaseUrl.'organizations';

        return $this->apiService->getRequest($url, true);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->data[0]->id;
    }

    public function getTickets()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'tickets?include=contacts,assignee,departments,team,isRead';

        return $this->apiService->getRequest($url, $this->orgId);
    }
}
