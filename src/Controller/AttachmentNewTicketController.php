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

class AttachmentNewTicketController extends AbstractController
{
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
