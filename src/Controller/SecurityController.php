<?php

namespace App\Controller;

use App\Service\PageService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        UserService $userService,
        PageService $pageService
    ) {
        $this->userService = $userService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/inloggen", name="app_login")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $user = $this->getUser();

        if (null !== $user) {
            return $this->redirectToRoute('app_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $welcomeText = 'Door aan de rechterzijde van het scherm in te loggen krijg je toegang.';
        dump($request->getPathInfo());

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'welcomeText' => $welcomeText,
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/uitloggen", name="app_logout")
     */
    public function logout(): RedirectResponse
    {
        $this->userService->logoutUser();

        return $this->redirectToRoute('app_login');
    }
}
