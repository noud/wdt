<?php

namespace App\Service\Zoho;

class ZohoDeskApiService
{
    /**
     * @var ZohoApiService
     */
    private $apiService;

    /**
     * @var string
     */
    private $orgId;

    public function __construct(ZohoApiService $zohoDeskApiService)
    {
        $this->apiService = $zohoDeskApiService;
    }

    public function getOrganizations()
    {
        $url = $this->apiService->apiBaseUrl.'organizations';

        return $this->apiService->getRequest($url, true);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->data[0]->id;
    }

    public function getTickets()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'tickets?include=contacts,assignee,departments,team,isRead';

        return $this->apiService->getRequest($url, $this->orgId);
    }
    
    public function getDepartments()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'departments?isEnabled=true&chatStatus=AVAILABLE';
        
        return $this->apiService->getRequest($url, $this->orgId);
    }
    
    public function getContacts()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'contacts';
        
        return $this->apiService->getRequest($url, $this->orgId);
    }
    
    public function createTicket()
    {
        $this->orgId = $this->getOrganizationId();
        $url = $this->apiService->apiBaseUrl.'tickets';
        $data = [
            "subject" => "TEST TEST TEST",
            "departmentId" => 22147000000007061,
            "contactId" => 22147000000078082,
//             "subCategory" => "Sub General",
//             "productId" => "",
//             "dueDate" => "2016-06-21T16:16:16.000Z",
//             "channel" => "Email",
//             "description" => "Hai This is Description",
//             "priority" => "High",
//             "classification" => "",
//             "assigneeId" => "1892000000056007",
//             "phone" => "1 888 900 9646",
//             "category" => "general",
//             "email" => "carol@zylker.com",
//             "status" => "Open",
        ];
        return $this->apiService->getRequest($url, $this->orgId, $data);
    }
}
