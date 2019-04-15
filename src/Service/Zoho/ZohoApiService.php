<?php

namespace App\Service\Zoho;

class ZohoApiService
{
    public $zohoAccessTokenService;
    /**
     * @var string
     */
    protected $apiBaseUrl;
    
    public function __construct(
        $apiBaseUrl,
        ZohoAccessTokenService $zohoAccessTokenService
    ) {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->apiBaseUrl = $apiBaseUrl;
    }
}
