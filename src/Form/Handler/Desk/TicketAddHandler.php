<?php

namespace App\Form\Handler\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Zoho\Service\Desk\TicketAttachmentService;
use App\Zoho\Service\Desk\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;
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
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;
    
    /**
     * JoinHandler constructor.
     */
    public function __construct(
        string $ticketAttachmentPath,
        EntityManagerInterface $entityManager,
        TicketService $ticketService,
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->entityManager = $entityManager;
        $this->ticketService = $ticketService;
        $this->ticketAttachmentService = $ticketAttachmentService;
        $this->ticketAttachmentPath = $ticketAttachmentPath;
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
            $ticketResponse = $this->ticketService->addTicket($ticketData);   // @TODO get the new ticket id
            $ticketId = $ticketResponse['id'];
            
            // now put the attachments
            $uploadFormId = $ticketData->uploadFormId;
            $dirName = $this->ticketAttachmentPath.'/'.$uploadFormId.'/';
            $files = Finder::create()
                ->files()
                ->in($dirName);
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $this->ticketAttachmentService->createTicketAttachment($dirName.$fileName, $ticketId);
                unlink($dirName.$fileName);
            }
            rmdir($dirName);
            
            return true;
        }

        return false;
    }
}
