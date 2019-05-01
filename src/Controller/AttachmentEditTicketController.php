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

class AttachmentEditTicketController extends AbstractController
{
    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    public function __construct(
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->ticketAttachmentService = $ticketAttachmentService;
    }

    /**
     * @Route("/attachment/post/{id}", methods={"POST"}, name="attachment_edit_post")
     */
    public function post(
        Request $request,
        PostAttachmentHandler $formHandler,
        int $id
    ): Response {
        $data = new PostAttachmentData();

        $form = $this->createForm(PostAttachmentType::class, $data);
        if ($formHandler->handleRequest($form, $request, $id)) {
            return new Response('', 201);
        }

        return new JsonResponse(
            [
                'error' => 'Upload is waarschijnlijk het verkeerde bestandstype.',
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/attachment/remove/{ticketId}/{attachmentId}", methods={"DELETE"}, name="attachment_edit_remove")
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
     * @Route("/attachment/remove/{ticketId}", name="attachment_edit_new_remove")
     */
    public function removeNew2(
        Request $request,
        AttachmentRemoveEditHandler $formHandler,
        int $ticketId
    ): Response {
        $data = new AttachmentRemoveEditData();

        $form = $this->createForm(AttachmentRemoveEditType::class, $data);

        if ($formHandler->handleRequest($form, $request, $ticketId)) {
            return new Response('', 200);
        }

        return new Response('', 404);
    }
}
