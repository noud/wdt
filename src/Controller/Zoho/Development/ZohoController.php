<?php

namespace App\Controller\Zoho\Development;

use App\Zoho\Api\ZohoAccessTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZohoController extends AbstractController
{
    /**
     * @var ZohoAccessTokenService
     */
    private $zohoAccessTokenService;

    public function __construct(
        ZohoAccessTokenService $zohoAccessTokenService
    ) {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
    }

    /**
     * @Route("/generate-access-token/{grantToken}", name="zoho_generate_access_token")
     */
    public function generateAccessToken(string $grantToken)
    {
        $this->zohoAccessTokenService->generateAccessToken($grantToken);

        return new Response(
            '<html><body>Grant Token gegenereerd.</body></html>'
        );
    }
}
