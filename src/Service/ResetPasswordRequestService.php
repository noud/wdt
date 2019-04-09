<?php

namespace App\Service;

use App\Entity\ResetPasswordRequest;
use App\Mailer\MailSender;
use App\Repository\ResetPasswordRequestRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordRequestService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MailSender
     */
    private $mailSender;

    /**
     * @var ResetPasswordRequestRepository
     */
    private $resetPasswordRequestRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailSender $mailSender,
        ResetPasswordRequestRepository $resetPasswordRequestRepository,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->mailSender = $mailSender;
        $this->resetPasswordRequestRepository = $resetPasswordRequestRepository;
        $this->translator = $translator;
    }

    public function addResetPasswordRequest(string $username): string
    {
        $resetPasswordRequest = new ResetPasswordRequest();
        $resetPasswordRequest->setUsername($username);
        $resetPasswordRequest->setToken(uniqid('', true));

        /** @var \DateTimeImmutable $expireDate */
        $expireDate = (new \DateTimeImmutable())::createFromFormat('U', (string) time());

        if ($expireDate instanceof \DateTimeImmutable) {
            $expireDate = $expireDate->add(new DateInterval('P1D'));
        }

        $resetPasswordRequest->setExpireDate($expireDate);

        $this->entityManager->persist($resetPasswordRequest);

        $this->resetPasswordRequestRepository->removeExpiredByUsername($username);

        $this->entityManager->flush();
        $token = $resetPasswordRequest->getToken();

        $subject = $this->translator->trans('reset_password.mail_subject.reset_password', [], 'reset_password');
        $this->mailSender->sendResetPasswordRequestMessage($subject, $username, $token);

        return $token;
    }

    public function findValidToken(string $token): ?ResetPasswordRequest
    {
        $resetPasswordRequest = $this->resetPasswordRequestRepository->findByValidToken($token);

        return $resetPasswordRequest;
    }

    public function removeExpired(): void
    {
        $this->resetPasswordRequestRepository->removeExpired();
    }
}
