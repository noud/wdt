<?php

namespace App\Controller;

use App\Service\PageService;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Form\Handler\Desk\TicketReplyAddHandler;
use App\Zoho\Form\Type\Desk\TicketReplyAddType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketReplyController extends AbstractController
{
    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        PageService $pageService,
        TranslatorInterface $translator
    ) {
        $this->pageService = $pageService;
        $this->translator = $translator;
    }

    /**
     * @Route("/ticket/{ticketId}/reply/create", name="ticket_reply_create")
     */
    public function createDeskTicketReply(string $ticketId, TicketReplyAddHandler $ticketReplyAddHandler, Request $request): Response
    {
        $data = new TicketCommentAddData();
        $form = $this->createForm(TicketReplyAddType::class, $data);

        if ($ticketReplyAddHandler->handleRequest($form, $request, $ticketId)) {
            $this->addFlash('success', $this->translator->trans('ticket.message.added', [], 'ticket'));

            return $this->redirectToRoute('ticket_reply_create_success');
        }

        return $this->render('ticket_reply/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug('/ticket/reply/create'),
        ]);
    }

    /**
     * @Route("/ticket/reply/create/success", name="ticket_reply_create_success")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addReplyThanks(Request $request): Response
    {
        return $this->render('ticket/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }
}
