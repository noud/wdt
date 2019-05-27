<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Service\ArrayService;
use App\Zoho\Service\Desk\AccountService;
use App\Zoho\Service\Desk\TicketAttachmentService;
use App\Zoho\Service\Desk\TicketService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TicketAttachmentVoter extends Voter
{
    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var TicketService
     */
    private $ticketService;

    public function __construct(
        TicketAttachmentService $ticketAttachmentService,
        AccountService $accountService,
        TicketService $ticketService
    ) {
        $this->ticketAttachmentService = $ticketAttachmentService;
        $this->accountService = $accountService;
        $this->ticketService = $ticketService;
    }

    protected function supports($attribute, $subject): bool
    {
        return 'TICKET_ATTACHMENT' === $attribute &&
            is_string($subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        //return true;
        /* @var Ticket $subject */
        dump($attribute);
        dump($subject);
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $email = $user->getUsername();
        $creatorId = $this->accountService->getAccountContactIdByEmail($email);

        // check if ticket belongs to user..
        $ticketAttachments = $this->ticketAttachmentService->getAllPublicTicketAttachments($subject['ticketId']);
        $key = ArrayService::searchArrayForId((string) $subject['attachmentId'], 'id', $ticketAttachments);
        if (null !== $key) {
            $ticket = $this->ticketService->getTicket($subject['ticketId']);
            if ($ticket['contactId'] === $creatorId) {
                return true;
            }
        }

        return false;
    }
}
