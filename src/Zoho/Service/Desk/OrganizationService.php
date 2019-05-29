<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Service\CacheService;

class OrganizationService
{
    /**
     * @var ZohoApiService
     */
    private $zohoApiService;

    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        CacheService $cacheService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->cacheService = $cacheService;
    }

    public function getAllOrganizations(): array
    {
        return $this->zohoApiService->get('organizations');
    }

    public function getOrganizationId(): ?int
    {
        $cacheKey = 'zoho_desk_organization_id';
        $hit = $this->cacheService->getFromCache($cacheKey);
        if (false === $hit) {
            $organizations = $this->getAllOrganizations();

            $organizationId = isset($organizations['data'][0]) ? $organizations['data'][0]['id'] : null;
            $this->cacheService->saveToCache($cacheKey, $organizationId);

            return $organizationId;
        }

        return $hit;
    }
}
