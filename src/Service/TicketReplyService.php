<?php

namespace App\Service;

use App\Mailer\MailSender;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;
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

    public function addTicketReply(TicketCommentAddData $ticketCommentData, string $ticketId, string $from)
    {
        $to = $this->supportEmailAddressService->getFirstSupportEmailAddress();

        $reply = $ticketCommentData->content;
        $ticket = $this->ticketService->getTicket($ticketId);
        $ticketNumber = $ticket['ticketNumber'];
        $subject = '[##' . $ticketNumber . '##]';
        //$subject = 'Re:[## '.$ticketNumber.' ##]';

        $this->mailSender->sendTicketReplyMessage($subject, $to, $reply, $from);
    }
}
