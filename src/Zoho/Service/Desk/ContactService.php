<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class ContactService
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

    public function getAllContacts(): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        $from = 0;
        $limit = 20;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('contacts', $organisationId, [
                'include' => 'accounts,contacts',
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
