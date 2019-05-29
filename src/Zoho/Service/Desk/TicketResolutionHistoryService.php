<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Service\CacheService;

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
     * @var CacheService
     */
    private $cacheService;

    /**
     * DepartmentService constructor.
     */
    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService,
        CacheService $cacheService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
        $this->cacheService = $cacheService;
    }

    public function getAllTicketResolutionHistory(int $ticketId)
    {
        $cacheKey = sprintf('zoho_desk_ticket_resolution_history_%s', md5((string) $ticketId));
        $hit = $this->cacheService->getFromCache($cacheKey);
        if (false === $hit) {
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

            $this->cacheService->saveToCache($cacheKey, $totalResult);

            return $totalResult;
        }

        return $hit;
    }
}
