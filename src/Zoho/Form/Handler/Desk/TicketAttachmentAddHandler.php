<?php

namespace App\Zoho\Form\Handler\Desk;

use App\Zoho\Form\Data\Desk\TicketAttachmentAddData;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TicketAttachmentAddHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->entityManager = $entityManager;
        $this->ticketAttachmentService = $ticketAttachmentService;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request, string $ticketId): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketAttachmentAddData $ticketAttachmentData */
            $ticketAttachmentData = $form->getData();
            $this->ticketAttachmentService->addTicketAttachment($ticketAttachmentData, $ticketId);

            return true;
        }

        return false;
    }
}
