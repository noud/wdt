<?php

namespace App\Form\Handler;

use App\Service\Zoho\ZohoDeskApiService;
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
     * @var ZohoDeskApiService
     */
    private $deskApiService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ZohoDeskApiService $deskApiService
    ) {
        $this->entityManager = $entityManager;
        $this->deskApiService = $deskApiService;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Form\Data\TicketAddData $ticketData */
            $ticketData = $form->getData();
            $this->deskApiService->addTicket($ticketData);

            return true;
        }

        return false;
    }
}
