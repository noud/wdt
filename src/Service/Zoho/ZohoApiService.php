<?php

namespace App\Service\Zoho;

class ZohoApiService
{
    /**
     * @var ZohoAccessTokenService
     */
    private $zohoAccessTokenService;

    /**
     * @var string
     */
    public $apiBaseUrl;

    public function __construct(
        ZohoAccessTokenService $zohoAccessTokenService,
        $apiBaseUrl
    ) {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function init(): void
    {
        $this->zohoAccessTokenService->init();
    }

    public function getRequest(string $url, $orgId = null, $data = null)
    {
        $this->zohoAccessTokenService->setAccessToken();

        if ($orgId) {
            $header = [
                'orgId: '.$orgId,
                'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
            ];
        } else {
            $header = [
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
            ];
        }

        /** @var resource $ch */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        /** @var string $result */
        $result = curl_exec($ch);
        $result = json_decode($result);
dump($result);
        if (!$orgId && 57 === $result->code) {
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
}
