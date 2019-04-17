<?php

namespace App\Service\Zoho;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ZohoAccessTokenService
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var string
     */
    private $currentUserEmail;

    /**
     * @var string
     */
    public $authPath;

    /**
     * @var string
     */
    public $logPath;

    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @var string
     */
    private $accountsUrl;

    /**
     * @var string
     */
    protected $grantToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var float
     */
    private $accessTokenExpiryTime;

    /**
     * Webservice constructor.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $currentUserEmail,
        string $authPath,
        string $logPath,
        string $apiBaseUrl,
        string $accountsUrl,
        string $grantToken,
        string $refreshToken
    ) {
        $this->grantToken = $grantToken;
        $this->refreshToken = $refreshToken;
        $this->currentUserEmail = $currentUserEmail;

        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->currentUserEmail = $currentUserEmail;
        $this->authPath = $authPath;
        $this->logPath = $logPath;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->accountsUrl = $accountsUrl;

        $filesystem = new Filesystem();
        $tokenPersistenceFileCreated = false;
        $tokenPersistenceFile = $this->authPath.'/zcrm_oauthtokens.txt';
        if (!$filesystem->exists($tokenPersistenceFile)) {
            try {
                $filesystem->mkdir($this->authPath);
                $filesystem->touch($tokenPersistenceFile);
                $tokenPersistenceFileCreated = true;
            } catch (IOExceptionInterface $exception) {
                throw new IOException('An error occurred while creating your auth file at '.$exception->getPath());
            }
        }
        $logFile = $this->logPath.'/ZCRMClientLibrary.log';
        if (!$filesystem->exists($logFile)) {
            try {
                $filesystem->mkdir($this->logPath);
                $filesystem->touch($logFile);
            } catch (IOExceptionInterface $exception) {
                throw new IOException('An error occurred while creating your log file at '.$exception->getPath());
            }
        }

        $this->init();

        if ($tokenPersistenceFileCreated) {
            $this->generateAccessToken();
        }
    }

    public function init()
    {
        $configuration = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'currentUserEmail' => $this->currentUserEmail,
            'token_persistence_path' => $this->authPath,    // zcrm_oauthtokens.txt
            'apiBaseUrl' => $this->apiBaseUrl,
            'accounts_url' => $this->accountsUrl,
            'applicationLogFilePath' => $this->logPath,
        ];

        \ZCRMRestClient::initialize($configuration);
    }

    public function generateAccessToken(string $grantToken = null)
    {
        $this->init();
        $this->grantToken = $grantToken ?: $this->grantToken;
        $oAuthClient = \ZohoOAuth::getClientInstance();
        $accessTokens = $oAuthClient->generateAccessToken($this->grantToken);
        $this->accessToken = $accessTokens->getAccessToken();
    }

    public function setAccessToken(): void
    {
        $file = $this->authPath.'/zcrm_oauthtokens.txt';
        if (file_exists($file)) {
            /** @var string $fileContent */
            $fileContent = file_get_contents($file);
            $fileArray = unserialize($fileContent);
            if ($fileArray) {
                try {
                    $this->accessToken = $fileArray[0]->getAccessToken();
                    $this->accessTokenExpiryTime = $fileArray[0]->getExpiryTime();
                    dump($this->accessTokenExpiryTime);
                } catch (\Exception $e) {
                    $this->generateAccessTokenFromRefreshToken();
                    $this->setRefreshToken();
                }
            }
        }
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getAccessTokenExpiryTime(): ?float
    {
        return $this->accessTokenExpiryTime;
    }

    private function setRefreshToken(): void
    {
        $file = $this->authPath.'/zcrm_oauthtokens.txt';
        if (file_exists($file)) {
            /** @var string $fileContent */
            $fileContent = file_get_contents($file);
            $fileArray = unserialize($fileContent);
            if ($fileArray) {
                try {
                    $this->refreshToken = $fileArray[0]->getRefreshToken();
                } catch (\Exception $e) {
                    throw new \Exception('setRefreshToken does not work.');
                }
            }
        }
    }

    public function generateAccessTokenFromRefreshToken()
    {
        $this->init();
        $oAuthClient = \ZohoOAuth::getClientInstance();
        $oAuthClient->generateAccessTokenFromRefreshToken($this->refreshToken, $this->currentUserEmail);
    }
}
