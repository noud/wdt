<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class OrganizationService
{
    /**
     * @var ZohoApiService
     */
    private $zohoDeskApiService;

    /**
     * OrganizationService constructor.
     */
    public function __construct(ZohoApiService $zohoDeskApiService)
    {
        $this->zohoDeskApiService = $zohoDeskApiService;
    }

    public function getAllOrganizations()
    {
        $this->zohoDeskApiService->setService('organizations');

        return $this->zohoDeskApiService->getRequest(true);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getAllOrganizations();
        dump($organizations);

        return $organizations['data'][0]['id'];
    }
}
