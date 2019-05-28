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

        $from = 0;
        $limit = 99;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('accounts', $organisationId, [
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

    public function getAccountIdByEmail(string $email): ?string
    {
        $cacheKey = sprintf('zoho_desk_account_id_%s', md5($email));
        $hit = $this->zohoApiService->getFromCache($cacheKey);
        if (false === $hit) {
            $accountId = null;
            $accounts = $this->getAllAccounts();

            foreach ($accounts as $account) {
                $accountContacts = $this->getAllAccountContacts($account['id']);
                foreach ($accountContacts as $contact) {
                    if (isset($contact['email']) && $contact['email'] === $email) {
                        $accountId = $account['id'];
                        break 2;
                    }
                }
            }
            $this->zohoApiService->saveToCache($cacheKey, $accountId);

            return $accountId;
        }

        return $hit;
    }

    public function getAllAccountContacts(string $accountId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        $from = 0;
        $limit = 100;
        $totalResult = [];

        while (true) {
            $result = $this->zohoApiService->get('accounts/'.$accountId.'/contacts', $organisationId, [
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

    public function getAccountContactIdByEmail(string $email): ?string
    {
        $accounts = $this->getAllAccounts();
        foreach ($accounts as $account) {
            $accountContacts = $this->getAllAccountContacts($account['id']);
            foreach ($accountContacts as $contact) {
                if (isset($contact['email']) && $contact['email'] === $email) {
                    return $contact['id'];
                }
            }
        }
    }
}
