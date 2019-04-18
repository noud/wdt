<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Service\ZohoDeskApiService;

class ContactService
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

    public function getAllContacts()
    {
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('contacts', [
            'include' => 'accounts',
        ]);

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
    }
}
