<?php

namespace App\Controller;

use App\Service\PageService;
use App\Zoho\Form\Data\Desk\TicketAttachmentAddData;
use App\Zoho\Form\Handler\Desk\TicketAttachmentAddHandler;
use App\Zoho\Form\Type\Desk\TicketAttachmentAddType;
use App\Zoho\Service\Desk\TicketAttachmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketAttachmentController extends AbstractController
{
    /**
     * @var PageService
     */
    private $pageService;
    private $ticketAttachmentService;
    public function __construct(
        PageService $pageService,
        TicketAttachmentService $ticketAttachmentService
    ) {
        $this->pageService = $pageService;
        $this->ticketAttachmentService = $ticketAttachmentService;
    }

    /**
     * @Route("/desk/tickets/attachment/create/{ticketId}", name="zoho_desk_tickets_attachment_create")
     */
    public function createDeskTicketAttachment(string $ticketId, TicketAttachmentAddHandler $ticketAttachmentAddHandler, Request $request): Response
    {
        $this->ticketAttachmentService->createTicketAttachment(null, $ticketId);
        return new Response(
            '<html><body>Attachment added.</body></html>'
            );
        
        $data = new TicketAttachmentAddData();
        $form = $this->createForm(TicketAttachmentAddType::class, $data);

        if ($ticketAttachmentAddHandler->handleRequest($form, $request, $ticketId)) {
            //die('adddd');
            $this->addFlash('success', 'Ticket comment is toegevoegd.');

            return $this->redirectToRoute('zoho_desk_tickets_comment_create_thanks');
        }

        return $this->render('desk/ticket_attachment/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($this->pathStripLastPart($request->getPathInfo())),
        ]);
    }

    /**
     * @Route("/desk/tickets/attachment/create-thanks", name="zoho_desk_tickets_attachment_create_thanks")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addCommentThanks(Request $request): Response
    {
        return $this->render('desk/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    private function pathStripLastPart(string $path): string
    {
        $slug = explode('/', $path);
        array_pop($slug);
        $path = implode('/', $slug);

        return $path;
    }
}
