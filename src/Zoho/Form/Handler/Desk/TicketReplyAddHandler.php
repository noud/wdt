<?php

namespace App\Zoho\Form\Handler\Desk;

use App\Zoho\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Form\Data\Desk\TicketReplyAddData;
//use App\Service\TicketReplyService;
use App\Zoho\Service\Desk\TicketThreadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TicketReplyAddHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TicketThreadService
     */
    private $ticketReplyService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TicketThreadService $ticketReplyService
    ) {
        $this->entityManager = $entityManager;
        $this->ticketReplyService = $ticketReplyService;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request, string $ticketId): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketCommentAddData $ticketReplyData */
            $ticketReplyData = $form->getData();
            //$this->ticketReplyService->addTicketReply($ticketReplyData, $ticketId);
            $this->ticketReplyService->addTicketThread($ticketReplyData, $ticketId);

            return true;
        }

        return false;
    }
}
