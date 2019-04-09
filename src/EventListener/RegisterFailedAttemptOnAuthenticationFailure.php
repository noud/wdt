<?php

namespace App\EventListener;

use App\Service\LockingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;

class RegisterFailedAttemptOnAuthenticationFailure implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LockingService
     */
    private $lockingService;

    /**
     * RegisterFailedAttemptOnAuthenticationFailure constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
        LockingService $lockingService
    ) {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->lockingService = $lockingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => ['registerFailedLoginAttempt', 1],
        ];
    }

    public function registerFailedLoginAttempt(): void
    {
        /** @var \Symfony\Component\HttpFoundation\Request $masterRequest */
        $masterRequest = $this->requestStack->getMasterRequest();
        $ipAddress = $masterRequest->getClientIp();
        $this->lockingService->addFailure((string) $ipAddress);
        $this->entityManager->flush();
    }
}
