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

    public function getAllDepartments()
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('departments', [
            'isEnabled' => 'true',
            'chatStatus' => 'AVAILABLE',
        ]);

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId());
    }

    public function getDepartmentId()
    {
        $departments = $this->getAllDepartments();

        return $departments['data'][0]['id'];
    }
}
