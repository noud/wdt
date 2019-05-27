<?php

namespace App\Service;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Mailer\MailSender;
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

    public function __construct(
        TicketService $ticketService,
        MailSender $mailSender,
        SupportEmailAddressService $supportEmailAddressService
    ) {
        $this->ticketService = $ticketService;
        $this->mailSender = $mailSender;
        $this->supportEmailAddressService = $supportEmailAddressService;
    }

    public function addTicketReply(TicketCommentAddData $ticketCommentData, int $ticketId, string $from): void
    {
        $to = $this->supportEmailAddressService->getFirstSupportEmailAddress();

        $reply = $ticketCommentData->content;
        $ticket = $this->ticketService->getTicket($ticketId);
        $ticketNumber = $ticket['ticketNumber'];
        $subject = '[##'.$ticketNumber.'##]'.' '.$ticket['subject'];

        $this->mailSender->sendTicketReplyMessage($subject, $to, $reply, $from);
    }
}
