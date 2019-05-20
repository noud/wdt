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

    /**
     * @var DepartmentService
     */
    private $departmentService;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService,
        DepartmentService $departmentService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
        $this->departmentService = $departmentService;
    }

    public function getAllSupportEmailAddresses(string $departmentId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('supportEmailAddress', $organisationId, [
            'departmentId' => $departmentId,
        ]);
    }

    public function getFirstSupportEmailAddress(): string
    {
        $departments = $this->departmentService->getAllDepartments();
        $supportEmailAddresses = $this->getAllSupportEmailAddresses($departments['data'][0]['id']);

        return $supportEmailAddresses['data'][0]['address'];
        // return 'support@wdtinternetbv.zohodesk.eu';   // @TODO get from API
    }
}
