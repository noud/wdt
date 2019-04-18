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
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('departments', [
            'isEnabled' => 'true',
            'chatStatus' => 'AVAILABLE',
        ]);

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
    }

    public function getDepartmentId()
    {
        $departments = $this->getAllDepartments();
        dump($departments);

        return $departments['data'][0]['id'];
    }
}
