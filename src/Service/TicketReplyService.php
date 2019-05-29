<?php

namespace App\Service;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Mailer\MailSender;
use App\Zoho\Service\CacheService;
use App\Zoho\Service\Desk\SupportEmailAddressService;
use App\Zoho\Service\Desk\TicketService;

class TicketReplyService
{
    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * @var MailSender
     */
    private $mailSender;

    /**
     * @var SupportEmailAddressService
     */
    private $supportEmailAddressService;

    /**
     * @var CacheService
     */
    private $cacheService;

    public function __construct(
        TicketService $ticketService,
        MailSender $mailSender,
        SupportEmailAddressService $supportEmailAddressService,
        CacheService $cacheService
    ) {
        $this->ticketService = $ticketService;
        $this->mailSender = $mailSender;
        $this->supportEmailAddressService = $supportEmailAddressService;
        $this->cacheService = $cacheService;
    }

    public function addTicketReply(TicketCommentAddData $ticketCommentData, int $ticketId, string $from)
    {
        $cacheKey = sprintf('zoho_desk_ticket_threads_%s', md5((string) $ticketId));
        $this->cacheService->deleteCacheByKey($cacheKey);

        $to = $this->supportEmailAddressService->getFirstSupportEmailAddress();

        $reply = $ticketCommentData->content;
        $ticket = $this->ticketService->getTicket($ticketId);
        $ticketNumber = $ticket['ticketNumber'];
        $subject = '[##'.$ticketNumber.'##]'.' '.$ticket['subject'];

        $this->mailSender->sendTicketReplyMessage($subject, $to, $reply, $from);
    }
}
