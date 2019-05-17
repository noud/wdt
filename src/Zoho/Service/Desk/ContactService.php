<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class ContactService
{
    /**
     * @var ZohoApiService
     */
    private $zohoApiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
    }

    public function getAllContacts(): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('contacts', $organisationId, [
            'include' => 'contacts',
        ]);
    }
}
