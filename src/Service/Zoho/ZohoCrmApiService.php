<?php

namespace App\Service\Zoho;

use App\Entity\User;

class ZohoCrmApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    public function __construct(ZohoApiService $zohoCrmApiService)
    {
        $this->apiService = $zohoCrmApiService;
    }

    public function hasAccessToPortal(User $user): bool
    {
        $email = $user->getEmail();

        $this->apiService->init();
        $rest = \ZCRMModule::getInstance('Contacts');
        $criteria = 'Email:equals:'.$email;
        try {
            $contacts = $rest->searchRecordsByCriteria($criteria)->getData();

            return $contacts[0]->getFieldValue('Toegang_tot_portal');
        } catch (\Exception $e) {
            return false;
        }
    }
}
