<?php

namespace App\Controller\Zoho\Development;

use App\Service\PageService;
use App\Zoho\Form\Data\Desk\TicketAddData;
use App\Zoho\Form\Handler\Desk\TicketAddHandler;
use App\Zoho\Form\Type\Desk\TicketAddType;
use App\Zoho\Service\ZohoDeskApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoDeskController extends AbstractController
{
    /**
     * @var ZohoDeskApiService
     */
    private $deskWebservice;

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        ZohoDeskApiService $zohoDeskService,
        PageService $pageService
    ) {
        $this->deskWebservice = $zohoDeskService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/desk/tickets/all", name="zoho_desk_tickets_all")
     */
    public function getDeskTicketsAll(): Response
    {
        $result = $this->deskWebservice->getTicketsAll();
        $ticketsInfo = '';
        foreach ($result->data as $ticket) {
            $ticketsInfo .= $ticket->ticketNumber.' '.$ticket->subject.'<br />';
        }

        return new Response(
            '<html><body>Tickets: <br />'.$ticketsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/organizations", name="zoho_desk_organizations")
     */
    public function getDeskOrganizations(): Response
    {
        $result = $this->deskWebservice->getOrganizations();
        $organizationsInfo = '';
        foreach ($result->data as $organization) {
            $organizationsInfo .= $organization->id.' '.$organization->companyName.'<br />';
        }

        return new Response(
            '<html><body>Organizations: <br />'.$organizationsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/departments", name="zoho_desk_departments")
     */
    public function getDeskDepartments(): Response
    {
        $result = $this->deskWebservice->getDepartments();
        $ticketsInfo = '';
        foreach ($result->data as $department) {
            $ticketsInfo .= $department->id.' '.$department->name.'<br />';
        }

        return new Response(
            '<html><body>Departments: <br />'.$ticketsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/contacts/index", name="zoho_desk_contacts")
     */
    public function getDeskContacts(): Response
    {
        $result = $this->deskWebservice->getContacts();
        $contactsInfo = '';
        foreach ($result->data as $contact) {
            $contactsInfo .= $contact->id.' '.$contact->email.'<br />';
        }

        return new Response(
            '<html><body>Contacts: <br />'.$contactsInfo.'</body></html>'
        );
    }

    /**
     * @Route("/desk/accounts/index", name="zoho_desk_accounts")
     */
    public function getDeskAccounts(): Response
    {
        $result = $this->deskWebservice->getAccounts();
        $accountsInfo = '';
        foreach ($result->data as $account) {
            $accountsInfo .= $account->id.' '.$account->accountName.' '.$account->email.'<br />';
        }

        return new Response(
            '<html><body>Accounts: <br />'.$accountsInfo.'</body></html>'
            );
    }

    /**
     * @Route("/desk/accounts/contacts/index/{accountId}", name="zoho_desk_accounts_contacts")
     */
    public function getDeskAccountContacts(string $accountId): Response
    {
        $result = $this->deskWebservice->getAccountContacts($accountId);
        $accountContactsInfo = '';
        foreach ($result->data as $accountContact) {
            $accountContactsInfo .= $accountContact->id.' '.$accountContact->lastName.' '.$accountContact->email.'<br />';
        }

        return new Response(
            '<html><body>Account contacts: <br />'.$accountContactsInfo.'</body></html>'
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
        $tickets = $this->deskWebservice->getTickets($email);

        return $this->render('ticket/overview.html.twig', [
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
            $this->addFlash('success', 'Ticket is toegevoegd.');

            return $this->redirectToRoute('zoho_desk_tickets_create_thanks');
        }

        return $this->render('ticket/create.html.twig', [
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
        return $this->render('ticket/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }
}
