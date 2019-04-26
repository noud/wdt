<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Service\ZohoDeskApiService;

class ResolutionHistoryService
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

    public function getAllResolutionHistory(string $ticketId)
    {
        $this->zohoDeskApiService->setOrganizationId();
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoDeskApiService->get('tickets/'.$ticketId.'/resolutionHistory', $organisationId);
    }
}
