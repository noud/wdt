<?php

namespace App\Zoho\Service\Desk;

use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\TicketThread;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;

class TicketThreadService
{
    /**
     * @var ZohoApiService
     */
    private $zohoApiService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var DepartmentService
     */
    private $departmentService;

    /**
     * @var SupportEmailAddressService
     */
    private $supportEmailAddressService;

    public function __construct(
        ZohoApiService $zohoDeskApiService,
        OrganizationService $organizationService,
        DepartmentService $departmentService,
        SupportEmailAddressService $supportEmailAddressService
    ) {
        $this->zohoApiService = $zohoDeskApiService;
        $this->organizationService = $organizationService;
        $this->departmentService = $departmentService;
        $this->supportEmailAddressService = $supportEmailAddressService;
    }

    public function getAllTicketThreads(string $ticketId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/threads', $organisationId);
    }

    public function getTicketThread(string $ticketId, string $threadId): array
    {
        $organisationId = $this->organizationService->getOrganizationId();

        return $this->zohoApiService->get('tickets/'.$ticketId.'/threads/'.$threadId, $organisationId, [
            'include' => 'plainText',
        ]);
    }

    private function sortTicketThreadsByDate(array $ticketThreads): array
    {
        usort($ticketThreads, function ($a, $b) {
            return $b['createdTime'] <=> $a['createdTime'];
        });

        return $ticketThreads;
    }

    public function getAllPublicTicketThreads(string $ticketId): array
    {
        $ticketThreads = $this->getAllTicketThreads($ticketId);
        $publicTicketThreads = array_filter($ticketThreads['data'], function ($comment) {
            return 'public' === $comment['visibility'];
        });

        $publicTicketThreads = $this->sortTicketThreadsByDate($publicTicketThreads);

        $ticketThreads = [];
        foreach ($publicTicketThreads as $publicTicketThread) {
            $ticketThread = $this->getTicketThread($ticketId, $publicTicketThread['id']);
            $ticketThreads[] = [
                'content' => $ticketThread['content'],
                'fromEmailAddress' => isset($ticketThread['fromEmailAddress']) ? $ticketThread['fromEmailAddress'] : '',
            ];
        }

        return $ticketThreads;
    }

    public function addTicketThread(TicketCommentAddData $ticketThreadData, string $ticketId, string $email)
    {
        $ticketThread = new TicketThread();
        $ticketThread->setContent($ticketThreadData->content);

        $this->createTicketThread($ticketThread, $ticketId, $email);
    }

    public function createTicketThread(TicketThread $ticketThread, string $ticketId, string $email)
    {
        $departments = $this->departmentService->getAllDepartments();
        $supportEmailAddresses = $this->supportEmailAddressService->getAllSupportEmailAddresses($departments['data'][0]['id']);
        $to = $supportEmailAddresses['data'][0]['address'];

        // @TODO activate this because noud@webdesigntilburg.nl does not get accepted.
//        $email = 'support@webdesigntilburg.nl';
//        $email = 'kevinvanderlaakwdt@gmail.com';
//        $email = 'support@wdtinternetbv.zohodesk.eu';
        
        $data = [
            'channel' => 'FORUMS',
            'content' => $ticketThread->getContent(),
            'contentType' => 'html',
            'fromEmailAddress' => $email,
            'to' => $to,
            'isPrivate' => "false",
            "isForward" => "true",
        ];
        dump($data); //die();

        $organisationId = $this->organizationService->getOrganizationId();

        $r = $this->zohoApiService->get('tickets/'.$ticketId.'/sendReply', $organisationId, [], $data);
        dump($r);die();
        return $r;
    }
}
