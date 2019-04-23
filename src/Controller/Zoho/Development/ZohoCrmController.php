<?php

namespace App\Controller\Zoho\Development;

use App\Entity\User;
use App\Zoho\Service\ZohoCrmApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoCrmController extends AbstractController
{
    /**
     * @var ZohoCrmApiService
     */
    private $zohoCrmApiService;

    public function __construct(
        ZohoCrmApiService $zohoCrmService
    ) {
        $this->zohoCrmApiService = $zohoCrmService;
    }

    /**
     * @Route("/zoho-has-access-to-portal/{email}", name="zoho_has_access_to_portal")
     */
    public function hasAccessToPortal(User $user): Response
    {
        $access = $this->zohoCrmApiService->hasAccessToPortal($user);

        return new Response(
            '<html><body>'.$access.'</body></html>'
        );
    }

    /**
     * @Route("/crm/contact/get-id/{email}", name="zoho_crm_contact_id")
     */
    public function getCrmContactId(User $user): Response
    {
        /** @var string $email */
        $email = $user->getEmail();
        $id = $this->zohoCrmApiService->getContactIdByEmail($email);

        return new Response(
            '<html><body>Id: '.$id.'</body></html>'
            );
    }
}
