<?php

namespace App\Controller;

use App\Form\Data\Desk\TicketCommentAddData;
use App\Form\Handler\Desk\TicketCommentAddHandler;
use App\Form\Type\Desk\TicketCommentAddType;
use App\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketCommentController extends AbstractController
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
     * @Route("/desk/tickets/comment/create/{ticketId}", name="zoho_desk_tickets_comment_create")
     */
    public function createDeskTicketComment(string $ticketId, TicketCommentAddHandler $ticketCommentAddHandler, Request $request): Response
    {
        $data = new TicketCommentAddData();
        $form = $this->createForm(TicketCommentAddType::class, $data);

        if ($ticketCommentAddHandler->handleRequest($form, $request, $ticketId)) {
            $this->addFlash('success', 'Ticket comment is toegevoegd.');

            return $this->redirectToRoute('zoho_desk_tickets_comment_create_thanks');
        }

        return $this->render('desk/ticket_comment/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($this->pathStripLastPart($request->getPathInfo())),
        ]);
    }

    /**
     * @Route("/desk/tickets/comment/create-thanks", name="zoho_desk_tickets_comment_create_thanks")
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
