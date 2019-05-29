<?php

namespace App\Form\Handler\Desk;

use App\Entity\User;
use App\Form\Data\Desk\TicketCommentAddData;
use App\Service\TicketReplyService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class TicketReplyAddHandler
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var TicketReplyService
     */
    private $ticketReplyService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        Security $security,
        TicketReplyService $ticketReplyService
    ) {
        $this->security = $security;
        $this->ticketReplyService = $ticketReplyService;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request, int $ticketId): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketCommentAddData $ticketReplyData */
            $ticketReplyData = $form->getData();
            /** @var TokenInterface $token */
            $token = $this->security->getToken();
            /** @var User $user */
            $user = $token->getUser();
            /** @var string $email */
            $email = $user->getEmail();
            // send with e-mail
            $this->ticketReplyService->addTicketReply($ticketReplyData, $ticketId, $email);

            return true;
        }

        return false;
    }
}
