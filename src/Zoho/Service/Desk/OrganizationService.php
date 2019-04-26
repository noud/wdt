<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class OrganizationService
{
    /**
     * @var ZohoApiService
     */
    private $zohoDeskApiService;

    /**
     * OrganizationService constructor.
     */
    public function __construct(ZohoApiService $zohoDeskApiService)
    {
        $this->zohoDeskApiService = $zohoDeskApiService;
    }

    public function getAllOrganizations(): array
    {
        return $this->zohoDeskApiService->get('organizations');
    }

    public function getOrganizationId(): ?int
    {
        $organizations = $this->getAllOrganizations();

        return isset($organizations['data'][0]) ? $organizations['data'][0]['id'] : null;
    }
}
