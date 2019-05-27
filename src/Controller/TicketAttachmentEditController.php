<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Form\Data\AttachmentRemoveEditData;
use App\Form\Handler\AttachmentRemoveEditHandler;
use App\Form\Handler\PostAttachmentHandler;
use App\Form\Type\AttachmentRemoveEditType;
use App\Form\Type\PostAttachmentType;
use App\Zoho\Service\Desk\AccountService;
use App\Zoho\Service\Desk\TicketAttachmentService;
use App\Zoho\Service\Desk\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketAttachmentEditController extends AbstractController
{
    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        TicketAttachmentService $ticketAttachmentService,
        AccountService $accountService,
        TicketService $ticketService,
        TranslatorInterface $translator
    ) {
        $this->ticketAttachmentService = $ticketAttachmentService;
        $this->accountService = $accountService;
        $this->ticketService = $ticketService;
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
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/ticket/attachment/remove/{ticketId}/{attachmentId}", methods={"DELETE"}, name="ticket_attachment_edit_remove")
     */
    public function remove(Request $request, int $ticketId, int $attachmentId): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('ticket-attachment-delete', $submittedToken)) {
            $params = [
                'ticketId' => $ticketId,
                'attachmentId' => $attachmentId,
            ];
            $this->denyAccessUnlessGranted('TICKET_ATTACHMENT', $params);
            $this->ticketAttachmentService->removeTicketAttachment($ticketId, $attachmentId);
        }

        return $this->redirectToRoute('ticket_view', ['id' => $ticketId]);
    }

    /**
     * @Route("/ticket/attachment/remove/{ticketId}", name="attachment_edit_new_remove")
     */
    public function removeNew(
        Request $request,
        AttachmentRemoveEditHandler $formHandler,
        int $ticketId
    ): Response {
        $this->denyAccessUnlessGranted('TICKET', $ticketId);
        $data = new AttachmentRemoveEditData();

        $form = $this->createForm(AttachmentRemoveEditType::class, $data);

        if ($formHandler->handleRequest($form, $request, $ticketId)) {
            return new Response('', 200);
        }
        throw new NotFoundHttpException($this->translator->trans('attachment.message.file_not_exist', [], 'attachment'));
    }
}
