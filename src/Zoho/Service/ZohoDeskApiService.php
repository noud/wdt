<?php

namespace App\Zoho\Service;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Service\Desk\OrganizationService;

class ZohoDeskApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var string
     */
    private $orgId;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService
    ) {
        $this->apiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
    }

    public function getRequest($orgId = null, $data = null)
    {
        return $this->apiService->getRequest($orgId, $data);
    }

    public function setService(string $slug, array $filters = [])
    {
        $this->apiService->setService($slug, $filters);
    }

    public function setOrgId()
    {
        $this->orgId = $this->organizationService->getOrganizationId();
    }

    public function getOrgId(): ?string
    {
        return $this->orgId;
    }
}
