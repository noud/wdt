<?php

namespace App\Controller;

use App\Form\Data\Desk\TicketAddData;
use App\Form\Handler\Desk\TicketAddHandler;
use App\Form\Type\Desk\TicketAddType;
use App\Service\PageService;
use App\Zoho\Service\Desk\ResolutionHistoryService;
use App\Zoho\Service\Desk\TicketAttachmentService;
use App\Zoho\Service\Desk\TicketCommentService;
use App\Zoho\Service\Desk\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketController extends AbstractController
{
    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * @var ResolutionHistoryService
     */
    private $resolutionHistoryService;

    /**
     * @var TicketCommentService
     */
    private $ticketCommentService;

    /**
     * @var TicketAttachmentService
     */
    private $ticketAttachmentService;

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        TicketService $ticketService,
        ResolutionHistoryService $resolutionHistoryService,
        TicketCommentService $ticketCommentService,
        TicketAttachmentService $ticketAttachmentService,
        PageService $pageService,
        TranslatorInterface $translator
    ) {
        $this->ticketService = $ticketService;
        $this->resolutionHistoryService = $resolutionHistoryService;
        $this->ticketCommentService = $ticketCommentService;
        $this->ticketAttachmentService = $ticketAttachmentService;
        $this->pageService = $pageService;
        $this->translator = $translator;
    }

    /**
     * @Route("/desk/tickets/all", name="zoho_desk_tickets_all")
     */
    public function getDeskTicketsAll(): Response
    {
        $result = $this->ticketService->getAllTickets();
        $ticketsInfo = '';
        foreach ($result['data'] as $ticket) {
            $ticketsInfo .= $ticket['ticketNumber'].' '.$ticket['subject'].'<br />';
        }

        return new Response(
            '<html><body>Tickets: <br />'.$ticketsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/ticket/overview", name="zoho_desk_tickets")
     */
    public function overview(Request $request): Response
    {
        $user = $this->getUser();
        /** @var string $email */
        $email = $user->getEmail();
        $tickets = $this->ticketService->getTickets($email);

        return $this->render('desk/ticket/overview.html.twig', [
            'tickets' => $tickets,
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/ticket/create", name="zoho_desk_tickets_create")
     */
    public function createDeskTicket(TicketAddHandler $ticketAddHandler, Request $request): Response
    {
        $user = $this->getUser();
        $data = new TicketAddData($user);
        $form = $this->createForm(TicketAddType::class, $data);

        if ($ticketAddHandler->handleRequest($form, $request)) {
            $this->addFlash('success', $this->translator->trans('ticket.message.added', [], 'ticket'));

            return $this->redirectToRoute('zoho_desk_tickets_create_thanks');
        }

        return $this->render('desk/ticket/create.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/desk/tickets/create-thanks", name="zoho_desk_tickets_create_thanks")
     */
    public function addThanks(Request $request): Response
    {
        return $this->render('desk/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/desk/tickets/view/{id}", name="zoho_desk_ticket_view")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function view(Request $request, string $id): Response
    {
        $ticket = $this->ticketService->getTicket($id);
        $resolutionHistory = $this->resolutionHistoryService->getAllResolutionHistory($id);
        $ticketComments = $this->ticketCommentService->getAllPublicTicketComments($id);
        $ticketAttachments = $this->ticketAttachmentService->getAllPublicTicketAttachments($id);

        return $this->render('desk/ticket/view.html.twig', [
            'ticket' => $ticket,
            'resolutionHistory' => $resolutionHistory,
            'ticketComments' => $ticketComments,
            'ticketAttachments' => $ticketAttachments,
            'page' => $this->pageService->getPageBySlug($this->pathStripLastPart($request->getPathInfo())),
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
