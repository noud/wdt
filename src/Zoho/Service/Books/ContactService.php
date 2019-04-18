<?php

namespace App\Zoho\Service\Books;

use App\Zoho\Api\ZohoApiService;

class ContactService
{
    /**
     * @var ZohoApiService
     */
    private $zohoBooksApiService;

    /**
     * ContactService constructor.
     */
    public function __construct(ZohoApiService $zohoBooksApiService)
    {
        $this->zohoBooksApiService = $zohoBooksApiService;
    }

    public function getAllContacts()
    {
        $this->zohoBooksApiService->setService('contacts');

        return $this->zohoBooksApiService->getRequest();
    }

    public function getContactById(int $contactId)
    {
        $this->zohoBooksApiService->setService('contacts/'.$contactId);

        return $this->zohoBooksApiService->getRequest();
    }
}
