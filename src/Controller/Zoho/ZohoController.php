<?php

namespace App\Controller\Zoho;

use App\Entity\User;
use App\Service\Zoho\ContactsWebservice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoController extends AbstractController
{
    /**
     * @var ContactsWebservice
     */
    private $contactsWebservice;

    public function __construct(
        ContactsWebservice $contactsWebservice
    ) {
        $this->contactsWebservice = $contactsWebservice;
    }

    /**
     * @Route("/zoho-has-access-to-portal/{email}", name="zoho_has_access_to_portal")
     */
    public function hasAccessToPortal(User $user): Response
    {
        $access = $this->contactsWebservice->hasAccessToPortal($user);

        return new Response(
            '<html><body>'.$access.'</body></html>'
        );
    }

    /**
     * @Route("/generate-access-token/{grantToken}", name="zoho_generate_access_token")
     */
    public function generateAccessToken(string $grantToken)
    {
        $this->contactsWebservice->generateAccessToken($grantToken);

        return new Response(
            '<html><body>Grant Token gegenereerd.</body></html>'
        );
    }
}
