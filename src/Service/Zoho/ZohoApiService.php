<?php

namespace App\Service\Zoho;

class ZohoApiService
{
    /**
     * @var string
     */
    protected $apiBaseUrl;
    
    public function __construct(ZohoAccessTokenService $zohoAccessTokenService, $apiBaseUrl)
    {
        $this->apiBaseUrl = $apiBaseUrl;
    }
}
