<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Service\ZohoDeskApiService;

class DepartmentService
{
    /**
     * @var ZohoDeskApiService
     */
    private $zohoDeskApiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoDeskApiService $deskApiService,
        OrganizationService $organizationService
    ) {
        $this->zohoDeskApiService = $deskApiService;
        $this->organizationService = $organizationService;
    }

    public function getAllDepartments(): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $organisationId = $this->zohoDeskApiService->getOrganizationId();

        return $this->zohoDeskApiService->get('departments', $organisationId, [
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
