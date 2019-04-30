<?php

namespace App\Controller;

use App\Service\PageService;
use App\Service\PathService;
use App\Zoho\Form\Data\Desk\TicketCommentAddData;
use App\Zoho\Form\Handler\Desk\TicketCommentAddHandler;
use App\Zoho\Form\Type\Desk\TicketCommentAddType;
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
     * @Route("/ticket/comment/create/{ticketId}", name="ticket_comment_create")
     */
    public function createDeskTicketComment(string $ticketId, TicketCommentAddHandler $ticketCommentAddHandler, Request $request): Response
    {
        $data = new TicketCommentAddData();
        $form = $this->createForm(TicketCommentAddType::class, $data);

        if ($ticketCommentAddHandler->handleRequest($form, $request, $ticketId)) {
            $this->addFlash('success', $this->translator->trans('ticket.message.added', [], 'ticket'));

            return $this->redirectToRoute('ticket_comment_create_success');
        }

        return $this->render('ticket_comment/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug(PathService::pathStripLastPart($request->getPathInfo())),
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
