<?php

namespace App\Controller\Zoho;

use App\Service\Zoho\ContactsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoController extends AbstractController
{
    /**
     * @var ContactsService
     */
    private $contactsService;

    public function __construct(
        ContactsService $contactsService
    ) {
        $this->contactsService = $contactsService;
    }

    /**
     * @Route("/zoho-has-access-to-portal/{email}", name="zoho_has_access_to_portal")
     */
    public function allContacts(string $email): Response
    {
        $access = $this->contactsService->hasAccessToPortal($email);

        return new Response(
            '<html><body>'.$access.'</body></html>'
        );
    }
}
