<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class SupportEmailAddressService
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

    public function getAllSupportEmailAddresses(string $departmentId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('supportEmailAddress', $organisationId, [
            'departmentId' => $departmentId,
        ]);
    }
}
