<?php

namespace App\Controller;

use App\Form\Data\Desk\TicketAddData;
use App\Form\Handler\Desk\TicketAddHandler;
use App\Form\Type\Desk\TicketAddType;
use App\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketCreateController extends AbstractController
{
    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        PageService $pageService
    ) {
        $this->pageService = $pageService;
    }

    /**
     * @Route("/ticket/create", name="ticket_create")
     */
    public function createTicket(TicketAddHandler $ticketAddHandler, Request $request): Response
    {
        $user = $this->getUser();
        $email = $user->getEmail();
        $data = new TicketAddData($user);
        $form = $this->createForm(TicketAddType::class, $data);

        if ($ticketAddHandler->handleRequest($form, $request, $email)) {
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
}
