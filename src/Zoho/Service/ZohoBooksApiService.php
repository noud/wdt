<?php

namespace App\Zoho\Service;

use App\Zoho\Api\ZohoApiService;

class ZohoBooksApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    public function __construct(ZohoApiService $zohoBooksApiService)
    {
        $this->apiService = $zohoBooksApiService;
    }

    public function getRequest($orgId = null, $data = null)
    {
        return $this->apiService->getRequest($orgId, $data);
    }

    public function setService(string $slug, array $filters = [])
    {
        $this->apiService->setService($slug, $filters);
    }
}
