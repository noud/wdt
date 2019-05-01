<?php

namespace App\Form\Handler;

use App\Form\Data\PostAttachmentData;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PostAttachmentHandler
{
    /**
     * @var string
     */
    private $ticketAttachmentPath;

    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    public function __construct(
        string $ticketAttachmentPath,
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->ticketAttachmentPath = $ticketAttachmentPath;
        $this->ticketAttachmentService = $ticketAttachmentService;
    }

    public function handleRequest(FormInterface $form, Request $request, int $id): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PostAttachmentData $data */
            $data = $form->getData();

            $fileName = $data->filename;
            $binary = $data->file;
            $dirName = $this->ticketAttachmentPath.\DIRECTORY_SEPARATOR.$id.\DIRECTORY_SEPARATOR;
            if (!is_dir($dirName)) {
                mkdir($dirName, 0777, true);
            }
            if (move_uploaded_file($binary, $dirName.$fileName)) {
                $this->ticketAttachmentService->createTicketAttachment($dirName.$fileName, $id);
                unlink($dirName.$fileName);
                rmdir($dirName);

                return true;
            }
        }

        return false;
    }
}
