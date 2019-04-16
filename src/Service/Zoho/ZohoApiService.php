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
    
    public function getRequest(string $url)
    {
        $this->zohoAccessTokenService->setAccessToken();
        
        /** @var resource $ch */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
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
}
