<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Form\Data\PostAttachmentData;
use App\Form\Handler\PostAttachmentHandler;
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
     * @Route("/attachment/post/{id}", methods={"POST"}, name="attachment_post")
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
     * @ Route("/attachment/remove/{ticketId}/{attachmentId}", methods={"DELETE"}, name="attachment_remove")
     * @Route("/attachment/remove/{ticketId}/{attachmentId}", name="attachment_remove")
     */
    public function remove(int $ticketId, int $attachmentId): Response
    {
        $this->ticketAttachmentService->removeTicketAttachment($ticketId, $attachmentId);

        return $this->redirectToRoute('zoho_desk_ticket_view', ['id' => $ticketId]);
    }

    /**
     * @Route("/attachment/remove/{$ticketId}", methods={"DELETE"}, name="attachment_new_remove")
     */
    public function removeNew(int $ticketId): Response
    {
        $this->ticketAttachmentService->removeTicketNewAttachment($ticketId);

        return new Response('', 201);
    }
}
