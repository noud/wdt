<?php

namespace App\Service\Zoho;

class ZohoBooksApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var string
     */
    private $organizationId;

    public function __construct(ZohoApiService $zohoBooksApiService)
    {
        $this->apiService = $zohoBooksApiService;
    }

    private function getRequest(string $url)
    {
        $this->apiService->zohoAccessTokenService->setAccessToken();

        /** @var resource $ch */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Authorization: Zoho-oauthtoken '.$this->apiService->zohoAccessTokenService->getAccessToken(),
        ]);
        /** @var string $result */
        $result = curl_exec($ch);
        $result = json_decode($result);

        if (57 === $result->code) {
            // @TODO check refresh the token..
            //$this->apiService->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
            // @TODO now i should re-call this method..
            //$this->getRequest($url);
            // @TODO and i should keep a timer about how many times..
            // now how do i recall this getRequest function?
            throw new \Exception('refresh the token..in getRequest..');
        }

        return $result;
    }

    public function getOrganizations()
    {
        $url = $this->apiService->apiBaseUrl.'organizations';

        return $this->getRequest($url);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->organizations[0]->organization_id;
    }

    public function getContacts()
    {
        $this->organizationId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'contacts?organization_id='.$this->organizationId;

        return $this->getRequest($url);
    }

    public function getInvoices()
    {
        $this->organizationId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'invoices?organization_id='.$this->organizationId;

        return $this->getRequest($url);
    }
}
