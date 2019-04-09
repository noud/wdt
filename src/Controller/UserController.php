<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Data\UserAddData;
use App\Form\Handler\UserAddHandler;
use App\Form\Type\UserAddType;
use App\Mailer\MailSender;
use App\Service\PageService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class UserController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * JoinHandler constructor.
     */
    public function __construct(
        UserService $userService,
        PageService $pageService
    ) {
        $this->userService = $userService;
        $this->pageService = $pageService;
    }

    /**
     * @Route("/register", name="user_register")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(UserAddHandler $userAddHandler, Request $request): Response
    {
        $data = new UserAddData();
        $form = $this->createForm(UserAddType::class, $data);

        if ($userAddHandler->handleRequest($form, $request)) {
            return $this->redirectToRoute('user_register_thanks');
        }

        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/register-thanks", name="user_register_thanks")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function addThanks(Request $request): Response
    {
        return $this->render('user/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }

    /**
     * @Route("/register-activate/{token}", name="user_register_activate")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function activate(User $user): Response
    {
        $user = $this->userService->activateAndEmail($user);

        return $this->redirectToRoute('user_register_activate_thanks');
    }

    /**
     * @Route("/register-activate-thanks", name="user_register_activate_thanks")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function activateThanks(Request $request): Response
    {
        return $this->render('user/thanks.html.twig', [
            'page' => $this->pageService->getPageBySlug($request->getPathInfo()),
        ]);
    }
}
