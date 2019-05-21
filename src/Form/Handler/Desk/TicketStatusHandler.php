<?php

namespace App\Form\Handler\Desk;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TicketStatusHandler
{
    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function handleRequest(FormInterface $form, Request $request): string
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketAddData $ticketData */
            $ticketStatusData = $form->getData();
            $status = $ticketStatusData->status;
            if ($status) {
                return $status;
            }
        }

        return '';
    }
}
