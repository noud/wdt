<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class SearchService
{
    /**
     * @var ZohoApiService
     */
    private $zohoApiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
    }

    public function search(string $searchStr, string $module): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        $params['searchStr'] = $searchStr;
        if (\mb_strlen($module)) {
            $params['module'] = $module;
        }

        return $this->zohoApiService->get('search', $organisationId, $params);
    }
}
