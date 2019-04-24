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
     * @var int
     */
    private $organizationId;

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

    public function setOrganizationId()
    {
        $this->organizationId = $this->organizationService->getOrganizationId();
    }

    public function getOrganizationId(): ?int
    {
        return $this->organizationId;
    }
}
