<?php

namespace App\Controller;

use App\Form\Data\RequestResetPasswordData;
use App\Form\Data\ResetPasswordData;
use App\Form\Handler\ResetPasswordHandler;
use App\Form\Handler\ResetPasswordRequestHandler;
use App\Form\Type\ResetPasswordRequestType;
use App\Form\Type\ResetPasswordType;
use App\Service\PageService;
use App\Service\ResetPasswordRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @var ResetPasswordRequestService
     */
    private $resetPasswordRequestService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct(
        ResetPasswordRequestService $resetPasswordRequestService,
        TranslatorInterface $translator,
        PageService $pageService
    ) {
        $this->resetPasswordRequestService = $resetPasswordRequestService;
        $this->translator = $translator;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/wachtwoord-vergeten", name="app_reset_password_request")
     */
    public function resetPasswordRequest(Request $request, ResetPasswordRequestHandler $resetPasswordRequestHandler): Response
    {
        $data = new RequestResetPasswordData();
        $form = $this->createForm(ResetPasswordRequestType::class, $data);

        if ($resetPasswordRequestHandler->handleRequest($form, $request)) {
            return $this->redirectToRoute('app_reset_password_request_thanks');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/wachtwoord-vergeten-bedankt", name="app_reset_password_request_thanks")
     */
    public function resetPasswordRequestThanks(Request $request): Response
    {
        return $this->render('security/reset_password_request_thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/wachtwoord-herstellen/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, ResetPasswordHandler $resetPasswordHandler): Response
    {
        $resetPasswordRequest = $this->resetPasswordRequestService->findValidToken($token);
        if (null === $resetPasswordRequest) {
            throw $this->createNotFoundException(
                $this->translator->trans('reset_password.message.invalid_token', [], 'reset_password')
            );
        }

        $data = new ResetPasswordData();
        $form = $this->createForm(ResetPasswordType::class, $data);

        if ($result = $resetPasswordHandler->handleRequest($form, $request, $resetPasswordRequest)) {
            $this->addFlash('success',
                $this->translator->trans(
                    'reset_password.message.password_changed %name%',
                    ['%name%' => $resetPasswordRequest->getUsername()],
                    'reset_password'
                )
            );

            return $result;
        }

        $path = $request->getPathInfo();
        $slug = explode('/', $path);

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug('/'.$slug[1]),
        ]);
    }
}
