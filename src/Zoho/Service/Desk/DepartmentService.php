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

        $from = 0;
        $limit = 200;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('departments', $organisationId, [
                'isEnabled' => 'true',
                'chatStatus' => 'AVAILABLE',
                'from' => $from,
                'limit' => $limit,
            ]);
            if (isset($result['data']) && \count($result['data'])) {
                $totalResult = array_merge($totalResult, $result['data']);
                $from += $limit;
            } else {
                break;
            }
        }

        return $totalResult;
    }

    public function getDepartmentId(): int
    {
        $departments = $this->getAllDepartments();

        return (isset($departments[0])) ? $departments[0]['id'] : null;
    }
}
