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
        $organisationId = $this->zohoDeskApiService->getOrganizationId();

        return $this->zohoDeskApiService->get('accounts', $organisationId);
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
        $organisationId = $this->zohoDeskApiService->getOrganizationId();

        return $this->zohoDeskApiService->get('accounts/'.$accountId.'/contacts', $organisationId);
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
