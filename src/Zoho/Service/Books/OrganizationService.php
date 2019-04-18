<?php

namespace App\Zoho\Service\Books;

use App\Zoho\Api\ZohoApiService;

class OrganizationService
{
    /**
     * @var ZohoApiService
     */
    private $zohoBooksApiService;

    /**
     * OrganizationService constructor.
     */
    public function __construct(ZohoApiService $zohoBooksApiService)
    {
        $this->zohoBooksApiService = $zohoBooksApiService;
    }

    public function getAllOrganizations()
    {
        $this->zohoBooksApiService->setService('organizations');

        return $this->zohoBooksApiService->getRequest();
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getAllOrganizations();

        return $organizations['organizations'][0]['organization_id'];
    }

    public function getOrganization()
    {
        $organizations = $this->getAllOrganizations();
        if (isset($organizations['organizations'])) {
            return reset($organizations['organizations']);
        }

        return null;
    }
}
