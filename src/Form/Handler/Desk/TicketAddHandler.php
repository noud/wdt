<?php

namespace App\Form\Handler\Desk;

use App\Form\Data\Desk\TicketAddData;
use App\Service\StringService;
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
     * @var string
     */
    private $ticketAttachmentPath;

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
    public function handleRequest(FormInterface $form, Request $request, string $email): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TicketAddData $ticketData */
            $ticketData = $form->getData();
            $ticketResponse = $this->ticketService->addTicket($ticketData, $email);
            $ticketId = $ticketResponse['id'];

            $attachments = [];
            $ticketAttachmentsString = $ticketData->attachments;
            if (null !== $ticketAttachmentsString) {
                $ticketAttachments = explode(',', $ticketAttachmentsString);
                foreach ($ticketAttachments as $ticketAttachmentString) {
                    $ticketAttachment = explode('|', $ticketAttachmentString);
                    if (isset($ticketAttachment[3])) {
                        $attachments[$ticketAttachment[3]] = $ticketAttachment[0];
                    }
                }

                // now put the attachments
                // prevent climbing the path
                $uploadFormId = $ticketData->uploadFormId;
                if (StringService::checkCharactersAndNumbersWithDot($uploadFormId)) {
                    $dirName = $this->ticketAttachmentPath.\DIRECTORY_SEPARATOR.$uploadFormId.\DIRECTORY_SEPARATOR;
                    try {
                        $files = Finder::create()
                            ->files()
                            ->in($dirName);
                        foreach ($files as $file) {
                            $fileName = $file->getFilename();
                            $actualFileName = $attachments[$file->getFilename()];
                            $this->ticketAttachmentService->createTicketAttachment($dirName.$fileName, $ticketId, $actualFileName);
                            unlink($dirName.$fileName);
                        }
                        rmdir($dirName);
                    } catch (\Exception $e) {
                    }
                }
            }

            return true;
        }

        return false;
    }
}
