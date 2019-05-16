<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;

class AccountService
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

    public function getAllAccounts(): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('accounts', $organisationId);
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
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('accounts/'.$accountId.'/contacts', $organisationId);
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
