<?php

namespace App\Form\Handler\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Zoho\Service\Desk\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TicketAddHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TicketService $ticketService
    ) {
        $this->entityManager = $entityManager;
        $this->ticketService = $ticketService;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketAddData $ticketData */
            $ticketData = $form->getData();
            $this->ticketService->addTicket($ticketData);

            return true;
        }

        return false;
    }
}
