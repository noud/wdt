<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Service\ZohoDeskApiService;

class AccountService
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

    public function getAllAccounts()
    {
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('accounts');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
    }

    public function getAccountIdByEmail(string $email): ?string
    {
        $accounts = $this->getAllAccounts();
        foreach ($accounts['data'] as $account) {
            $accountContacts = $this->getAllAccountContacts($account['id']);
            foreach ($accountContacts['data'] as $contact) {
                if ($contact['email'] === $email) {
                    return $account['id'];
                }
            }
        }

        return null;
    }

    public function getAllAccountContacts(string $accountId)
    {
        $this->zohoDeskApiService->setOrgId();
        $this->zohoDeskApiService->setService('accounts/'.$accountId.'/contacts');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrgId());
    }

    public function getAccountContactIdByEmail(string $email): ?string
    {
        $accounts = $this->getAllAccounts();
        foreach ($accounts['data'] as $account) {
            $accountContacts = $this->getAllAccountContacts($account['id']);
            foreach ($accountContacts['data'] as $contact) {
                if ($contact['email'] === $email) {
                    return $contact['id'];
                }
            }
        }

        return null;
    }
}
