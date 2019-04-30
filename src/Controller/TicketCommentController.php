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
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketCommentController extends AbstractController
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
     * @Route("/ticket/{ticketId}/comment/create", name="ticket_comment_create")
     */
    public function createDeskTicketComment(int $ticketId, TicketCommentAddHandler $ticketCommentAddHandler, Request $request): Response
    {
        $data = new TicketCommentAddData();
        $form = $this->createForm(TicketCommentAddType::class, $data);

        if ($ticketCommentAddHandler->handleRequest($form, $request, $ticketId)) {
            $this->addFlash('success', $this->translator->trans('ticket.message.added', [], 'ticket'));

            return $this->redirectToRoute('ticket_comment_create_success');
        }

        return $this->render('ticket_comment/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug('/ticket/comment/create'),
        ]);
    }

    /**
     * @Route("/ticket/comment/create/success", name="ticket_comment_create_success")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addCommentThanks(Request $request): Response
    {
        return $this->render('ticket/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }
}
