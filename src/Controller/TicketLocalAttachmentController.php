<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Form\Data\AttachmentRemoveNewData;
use App\Form\Handler\AttachmentRemoveNewHandler;
use App\Form\Type\AttachmentRemoveNewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketLocalAttachmentController extends AbstractController
{
    /**
     * Remove a local attachment, that's not uploaded to Zoho but saved locally until the ticket is created.
     * @Route("/ticket/attachment/remove", name="ticket_local_attachment_remove")
     */
    public function remove(
        Request $request,
        AttachmentRemoveNewHandler $formHandler
    ): Response {
        $form = $this->createForm(AttachmentRemoveNewType::class);

        if ($formHandler->handleRequest($form, $request)) {
            return new Response('', 200);
        }

        return new Response('', 404);
    }
}
