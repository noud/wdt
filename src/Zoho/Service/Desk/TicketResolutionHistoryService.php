<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class TicketResolutionHistoryService
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

    public function getAllTicketResolutionHistory(int $ticketId)
    {
        $organisationId = $this->organizationService->getOrganizationId();

        $from = 0;
        $limit = 99;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('tickets/'.$ticketId.'/resolutionHistory', $organisationId, [
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
}
