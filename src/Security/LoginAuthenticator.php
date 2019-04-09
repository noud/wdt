<?php

namespace App\Security;

use App\Entity\User;
//use App\Service\LockingService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class LoginAuthenticator extends AbstractFormLoginAuthenticator implements AuthenticatorInterface
{
    use TargetPathTrait;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @ var LockingService
     */
    //private $lockingService;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * LoginAuthenticator constructor.
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        //LockingService $lockingService,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TranslatorInterface $translator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        //$this->lockingService = $lockingService;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        $route = $request->attributes->get('_route');

        return \in_array($route, ['app_login'], true) && $request->isMethod('POST');
    }

    private function isIpAddressLocked(Request $request): bool
    {
        //$ipAddress = (string)
        $request->getClientIp();

        return false;
        //return $this->lockingService->checkIsLockedIpAddress($ipAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request): array
    {
        // Check if the account is locked
        if ($this->isIpAddressLocked($request)) {
            throw new LockedException($this->translator->trans('login.messages.locked', [], 'login'));
        }

        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        if (null !== $request->getSession()) {
            $request->getSession()->set(
                Security::LAST_USERNAME,
                $credentials['email']
            );
        }

        return $credentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException($this->translator->trans('login.messages.token_invalid', [], 'login'));
        }

        // Load / create our user however you need.
        // You can do this by calling the user provider, or with custom logic here.
        /** @var User $user */
        $user = $userProvider->loadUserByUsername($credentials['email']);

        if (!$user instanceof User) {
            throw new CustomUserMessageAuthenticationException($this->translator->trans('login.messages.user_not_found', [], 'login'));
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException($this->translator->trans('login.messages.user_not_active', [], 'login'));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!$this->userPasswordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException($this->translator->trans('login.messages.data_invalid', [], 'login'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        //$ipAddress = $request->getClientIp();
        //$this->lockingService->removeFailedIp((string) $ipAddress);

        return new RedirectResponse($this->urlGenerator->generate('app_index'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
