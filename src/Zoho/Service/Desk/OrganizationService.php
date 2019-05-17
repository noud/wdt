<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class OrganizationService
{
    /**
     * @var ZohoApiService
     */
    private $zohoApiService;

    public function __construct(ZohoApiService $zohoDeskApiService)
    {
        $this->zohoApiService = $zohoDeskApiService;
    }

    public function getAllOrganizations(): array
    {
        return $this->zohoApiService->get('organizations');
    }

    public function getOrganizationId(): ?int
    {
        $organizations = $this->getAllOrganizations();

        return isset($organizations['data'][0]) ? $organizations['data'][0]['id'] : null;
    }
}
