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

    public function getAllResolutionHistory(int $ticketId)
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('tickets/'.$ticketId.'/resolutionHistory');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId());
    }
}
