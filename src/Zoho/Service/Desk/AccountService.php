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

    public function getAllAccounts(): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('accounts');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId());
    }

    public function getAccountIdByEmail(string $email): ?string
    {
        $accounts = $this->getAllAccounts();
        foreach ($accounts['data'] as $account) {
            $accountContacts = $this->getAllAccountContacts($account['id']);
            foreach ($accountContacts['data'] as $contact) {
                if (isset($contact['email']) && $contact['email'] === $email) {
                    return $account['id'];
                }
            }
        }
    }

    public function getAllAccountContacts(string $accountId): array
    {
        $this->zohoDeskApiService->setOrganizationId();
        $this->zohoDeskApiService->setService('accounts/'.$accountId.'/contacts');

        return $this->zohoDeskApiService->getRequest($this->zohoDeskApiService->getOrganizationId());
    }

    public function getAccountContactIdByEmail(string $email): ?string
    {
        $accounts = $this->getAllAccounts();
        foreach ($accounts['data'] as $account) {
            $accountContacts = $this->getAllAccountContacts($account['id']);
            foreach ($accountContacts['data'] as $contact) {
                if (isset($contact['email']) && $contact['email'] === $email) {
                    return $contact['id'];
                }
            }
        }
    }
}
