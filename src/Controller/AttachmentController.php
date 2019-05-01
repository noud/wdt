<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Form\Data\AttachmentRemoveNewData;
use App\Form\Data\PostAttachmentData;
use App\Form\Handler\AttachmentRemoveNewHandler;
use App\Form\Handler\PostAttachmentHandler;
use App\Form\Type\AttachmentRemoveNewType;
use App\Form\Type\PostAttachmentType;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AttachmentController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    public function __construct(
        EntityManagerInterface $entityManager,
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->entityManager = $entityManager;
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
        AttachmentRemoveNewHandler $formHandler,
        int $ticketId
    ): Response {
        $data = new AttachmentRemoveNewData();

        $form = $this->createForm(AttachmentRemoveNewType::class, $data);

        if ($formHandler->handleRequest($form, $request, $ticketId)) {
            return new Response('', 200);
        }

        return new Response('', 404);
    }

    /**
     * @Route("/attachment/remove", name="attachment_new_new_remove")
     */
    public function removeNew(
        Request $request,
        AttachmentRemoveNewHandler $formHandler
    ): Response {
        $data = new AttachmentRemoveNewData();

        $form = $this->createForm(AttachmentRemoveNewType::class, $data);

        if ($formHandler->handleRequest($form, $request)) {
            return new Response('', 200);
        }

        return new Response('', 404);
    }
}
