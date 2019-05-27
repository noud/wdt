<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Zoho\Service\Desk\AccountService;
use App\Zoho\Service\Desk\TicketService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TicketVoter extends Voter
{
    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var TicketService
     */
    private $ticketService;

    public function __construct(
        AccountService $accountService,
        TicketService $ticketService
    ) {
        $this->accountService = $accountService;
        $this->ticketService = $ticketService;
    }

    protected function supports($attribute, $subject): bool
    {
        return 'TICKET' === $attribute &&
            \is_int($subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $email = $user->getUsername();
        $creatorId = $this->accountService->getAccountContactIdByEmail($email);
        // check if ticket belongs to user..
        $ticket = $this->ticketService->getTicket($subject);
        if ($ticket['contactId'] === (string) $creatorId) {
            return true;
        }

        return false;
    }
}
