<?php

namespace App\Service\Zoho;

class ZohoApiService
{
    public $zohoAccessTokenService;
    /**
     * @var string
     */
    protected $apiBaseUrl;
    
    public function __construct(ZohoAccessTokenService $zohoAccessTokenService, $apiBaseUrl)
    {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->apiBaseUrl = $apiBaseUrl;
    }
}
