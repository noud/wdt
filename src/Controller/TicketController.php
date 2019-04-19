<?php

namespace App\Controller;

use App\Service\PageService;
use App\Zoho\Form\Data\Desk\TicketAddData;
use App\Zoho\Form\Handler\Desk\TicketAddHandler;
use App\Zoho\Form\Type\Desk\TicketAddType;
use App\Zoho\Service\Desk\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        TicketService $ticketService,
        PageService $pageService
    ) {
        $this->ticketService = $ticketService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/desk/tickets/all", name="zoho_desk_tickets_all")
     */
    public function getDeskTicketsAll()
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
     * @Route("/desk/tickets/index", name="zoho_desk_tickets")
     */
    public function getDeskTickets(Request $request)
    {
        $user = $this->getUser();
        /** @var string $email */
        $email = $user->getEmail();
        $tickets = $this->ticketService->getTickets($email);

        return $this->render('desk/ticket/index.html.twig', [
            'tickets' => $tickets,
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/desk/tickets/create", name="zoho_desk_tickets_create")
     */
    public function createDeskTicket(TicketAddHandler $ticketAddHandler, Request $request): Response
    {
        $user = $this->getUser();
        $data = new TicketAddData($user);
        $form = $this->createForm(TicketAddType::class, $data);

        if ($ticketAddHandler->handleRequest($form, $request)) {
            $this->addFlash('success', 'Ticket is toegevoegd.');

            return $this->redirectToRoute('zoho_desk_tickets_create_thanks');
        }

        return $this->render('desk/ticket/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/desk/tickets/create-thanks", name="zoho_desk_tickets_create_thanks")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addThanks(Request $request): Response
    {
        return $this->render('desk/ticket/thanks.html.twig', [
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

        $path = $request->getPathInfo();
        $slug = explode('/', $path);
        array_pop($slug);
        $path = implode('/', $slug);

        return $this->render('desk/ticket/view.html.twig', [
            'ticket' => $ticket,
            'page' => $this->pageService->getPageBySlug($path),
        ]);
    }
}
