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
        $cacheKey = 'zoho_desk_organization_id';
        $hit = $this->zohoApiService->getFromCache($cacheKey);
        if (false === $hit) {
            $organizations = $this->getAllOrganizations();

            $organizationId = isset($organizations['data'][0]) ? $organizations['data'][0]['id'] : null;
            $this->zohoApiService->saveToCache($cacheKey, $organizationId);

            return $organizationId;
        }

        return $hit;
    }
}
