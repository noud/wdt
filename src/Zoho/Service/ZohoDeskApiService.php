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
     * @var ?int
     */
    private $organizationId;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService
    ) {
        $this->apiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
    }

    public function get(string $slug, ?int $organizationId = null, array $filters = [], $data = null): array
    {
        return $this->apiService->get($slug, $organizationId, $filters, $data);
    }

    public function getOrganizations(): array
    {
        return $this->apiService->get('organizations');
    }

    public function getOrganizationId(): ?int
    {
        return $this->organizationId;
    }

    public function setOrganizationId(): void
    {
        $this->organizationId = $this->organizationService->getOrganizationId();
    }
}
