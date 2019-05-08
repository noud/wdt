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
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketLocalAttachmentController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }
    
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

        throw $this->createNotFoundException($this->translator->trans('attachment.message.file_not_exist', [], 'attachment'));
    }
}
