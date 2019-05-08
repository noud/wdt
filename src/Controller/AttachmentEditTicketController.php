<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Form\Data\AttachmentRemoveEditData;
use App\Form\Data\PostAttachmentData;
use App\Form\Handler\AttachmentRemoveEditHandler;
use App\Form\Handler\PostAttachmentHandler;
use App\Form\Type\AttachmentRemoveEditType;
use App\Form\Type\PostAttachmentType;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttachmentEditTicketController extends AbstractController
{
    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;
    
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    public function __construct(
        TicketAttachmentService $ticketAttachmentService,
        TranslatorInterface $translator
    ) {
        $this->ticketAttachmentService = $ticketAttachmentService;
        $this->translator = $translator;
    }

    /**
     * @Route("/ticket/attachment/post/{id}", methods={"POST"}, name="ticket_attachment_edit")
     */
    public function edit(
        Request $request,
        PostAttachmentHandler $formHandler,
        int $id
    ): Response {
        $form = $this->createForm(PostAttachmentType::class);
        if ($formHandler->handleRequest($form, $request, $id)) {
            return new Response('', 201);
        }

        return $this->json(
            [
                'error' => $this->translator->trans('attachment.message.file_type', [], 'attachment'),
            ]
        );
    }

    /**
     * @Route("/ticket/attachment/remove/{ticketId}/{attachmentId}", methods={"DELETE"}, name="ticket_attachment_edit_remove")
     */
    public function remove(Request $request, int $ticketId, int $attachmentId): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('ticket-attachment-delete', $submittedToken)) {
            $this->ticketAttachmentService->removeTicketAttachment($ticketId, $attachmentId);
        }

        return $this->redirectToRoute('ticket_view', ['id' => $ticketId]);
    }

    /**
     * @Route("/ticket/attachment/remove/{ticketId}", name="attachment_edit_new_remove")
     *
     * @throws HttpNotFoundException
     */
    public function removeNew(
        Request $request,
        AttachmentRemoveEditHandler $formHandler,
        int $ticketId
    ): Response {
        $data = new AttachmentRemoveEditData();

        $form = $this->createForm(AttachmentRemoveEditType::class, $data);

        if ($formHandler->handleRequest($form, $request, $ticketId)) {
            return new Response('', 200);
        }
        throw new HttpNotFoundException($this->translator->trans('attachment.message.file_not_exist', [], 'attachment'));
    }
}
