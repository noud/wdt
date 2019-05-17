<?php

namespace App\Service;

use App\Mailer\MailSender;
use App\Zoho\Api\ZohoApiService;
use App\Zoho\Entity\Desk\TicketReply;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Form\Data\Desk\TicketReplyAddData;
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
    
    public function __construct(
        TicketService $ticketService,
        MailSender $mailSender
    ) {
        $this->ticketService = $ticketService;
        $this->mailSender = $mailSender;
    }

    public function addTicketReply(TicketCommentAddData $ticketCommentData, string $ticketId, string $from)
    {
        $reply = $ticketCommentData->content;
        $ticket = $this->ticketService->getTicket($ticketId);
        $ticketNumber = $ticket['ticketNumber'];
        //$subject = '[##' . $ticketNumber . '##]';
        $subject = 'Re:[## ' . $ticketNumber . ' ##]';
        $email = 'support@wdtinternetbv.zohodesk.eu';   // @TODO get from API
        $this->mailSender->sendTicketReplyMessage($subject, $email, $reply, $from);
    }
}
