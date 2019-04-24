<?php

namespace App\Form\Handler\Desk;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Service\Desk\TicketCommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TicketCommentAddHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TicketCommentService
     */
    private $ticketCommentService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TicketCommentService $ticketCommentService
    ) {
        $this->entityManager = $entityManager;
        $this->ticketCommentService = $ticketCommentService;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request, string $ticketId): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketCommentAddData $ticketCommentData */
            $ticketCommentData = $form->getData();
            $this->ticketCommentService->addTicketComment($ticketCommentData, $ticketId);

            return true;
        }

        return false;
    }
}
