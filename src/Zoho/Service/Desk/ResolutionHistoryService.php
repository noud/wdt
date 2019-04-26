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
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoDeskApiService $deskApiService
    ) {
        $this->zohoDeskApiService = $deskApiService;
    }

    public function getAllResolutionHistory(string $ticketId)
    {
        $this->zohoDeskApiService->setOrganizationId();
        $organisationId = $this->zohoDeskApiService->getOrganizationId();

        return $this->zohoDeskApiService->get('tickets/'.$ticketId.'/resolutionHistory', $organisationId);
    }
}
