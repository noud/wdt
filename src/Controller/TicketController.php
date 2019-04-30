<?php

namespace App\Controller;

use App\Form\Data\Desk\TicketAddData;
use App\Form\Handler\Desk\TicketAddHandler;
use App\Form\Type\Desk\TicketAddType;
use App\Service\PageService;
use App\Zoho\Service\Desk\ResolutionHistoryService;
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
        PageService $pageService,
        TranslatorInterface $translator
    ) {
        $this->ticketService = $ticketService;
        $this->resolutionHistoryService = $resolutionHistoryService;
        $this->ticketCommentService = $ticketCommentService;
        $this->pageService = $pageService;
        $this->translator = $translator;
    }

    /**
     * @Route("/ticket/overview", name="ticket_overview")
     */
    public function overview(Request $request): Response
    {
        $user = $this->getUser();
        /** @var string $email */
        $email = $user->getEmail();
        $tickets = $this->ticketService->getTickets($email);

        return $this->render('ticket/overview.html.twig', [
            'tickets' => $tickets,
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/ticket/create", name="ticket_create")
     */
    public function createTicket(TicketAddHandler $ticketAddHandler, Request $request): Response
    {
        $user = $this->getUser();
        $data = new TicketAddData($user);
        $form = $this->createForm(TicketAddType::class, $data);

        if ($ticketAddHandler->handleRequest($form, $request)) {
            $this->addFlash('success', 'ticket.message.added');

            return $this->redirectToRoute('ticket_create_thanks');
        }

        return $this->render('ticket/create.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/ticket/create-thanks", name="ticket_create_thanks")
     */
    public function addThanks(Request $request): Response
    {
        return $this->render('ticket/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/ticket/view/{id}", name="ticket_view")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function view(string $id): Response
    {
        $ticket = $this->ticketService->getTicket($id);
        $resolutionHistory = $this->resolutionHistoryService->getAllResolutionHistory($id);
        $ticketComments = $this->ticketCommentService->getAllPublicTicketComments($id);

        return $this->render('ticket/view.html.twig', [
            'ticket' => $ticket,
            'resolutionHistory' => $resolutionHistory,
            'ticketComments' => $ticketComments,
            'page' => $this->pageService->getPageBySlug('/ticket/view'),
        ]);
    }
}
