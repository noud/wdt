<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class DepartmentService
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

    public function getAllDepartments(): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('departments', $organisationId, [
            'isEnabled' => 'true',
            'chatStatus' => 'AVAILABLE',
        ]);
    }

    public function getDepartmentId(): int
    {
        $departments = $this->getAllDepartments();

        return (isset($departments['data'][0])) ? $departments['data'][0]['id'] : null;
    }
}
