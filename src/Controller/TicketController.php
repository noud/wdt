<?php

namespace App\Controller;

use App\Form\Data\Desk\TicketStatusData;
use App\Form\Handler\Desk\TicketStatusHandler;
use App\Form\Type\Desk\TicketStatusType;
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
     * @Route("/ticket/overview", name="ticket_overview")
     */
    public function overviewWithStatus(TicketStatusHandler $ticketStatusHandler, Request $request): Response
    {
        $user = $this->getUser();
        /** @var string $email */
        $email = $user->getEmail();

        $data = new TicketStatusData();
        $form = $this->createForm(TicketStatusType::class, $data);

        if ($status = $ticketStatusHandler->handleRequest($form, $request)) {
            $tickets = $this->ticketService->searchTickets($email, $status);
        } else {
            $tickets = $this->ticketService->getTickets($email);
        }

        return $this->render('ticket/overview.html.twig', [
            'form' => $form->createView(),
            'tickets' => $tickets,
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/ticket/view/{ticketId}", name="ticket_view")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function view(int $ticketId): Response
    {
        $this->denyAccessUnlessGranted('TICKET', $ticketId);
        $ticket = $this->ticketService->getTicket($ticketId);
        $resolutionHistory = $this->resolutionHistoryService->getAllResolutionHistory($ticketId);
        $ticketComments = $this->ticketCommentService->getAllPublicTicketComments($ticketId);
        $ticketAttachments = $this->ticketAttachmentService->getAllPublicTicketAttachments($ticketId);

        return $this->render('ticket/view.html.twig', [
            'ticket' => $ticket,
            'resolutionHistory' => $resolutionHistory,
            'ticketComments' => $ticketComments,
            'ticketAttachments' => $ticketAttachments,
            'page' => $this->pageService->getPageBySlug('/ticket/view'),
        ]);
    }
}
