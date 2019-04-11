<?php

namespace App\Service\Zoho;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class Webservice
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
    private $logPath;

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
    private $grantToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * Webservice constructor.
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $currentUserEmail,
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
        $this->logPath = $logPath;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->accountsUrl = $accountsUrl;

        $filesystem = new Filesystem();
        $tokenPersistenceFileCreated = false;
        $tokenPersistenceFile = $this->logPath.'/zcrm_oauthtokens.txt';
        if (!$filesystem->exists($tokenPersistenceFile)) {
            try {
                $filesystem->mkdir($this->logPath);
                $filesystem->touch($tokenPersistenceFile);
                $filesystem->touch($this->logPath.'/ZCRMClientLibrary.log');
                $tokenPersistenceFileCreated = true;
            } catch (IOExceptionInterface $exception) {
                echo 'An error occurred while creating your file at '.$exception->getPath();
            }
        }
        // sometimes i need
        // /var/www/klantportaal/vendor/zohocrm/php-sdk/src/com/zoho/oauth/common/../logger/OAuth.log

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
            'token_persistence_path' => $this->logPath,    // zcrm_oauthtokens.txt
            'apiBaseUrl' => $this->apiBaseUrl,
            'accounts_url' => $this->accountsUrl,
            'applicationLogFilePath' => $this->logPath,
        ];

        \ZCRMRestClient::initialize($configuration);
    }

    public function generateAccessToken(string $grantToken = null)
    {
        // SCOPE = aaaserver.profile.ALL,ZohoCRM.modules.ALL
        $this->init();
        $this->grantToken = $grantToken ?: $this->grantToken;
        $oAuthClient = \ZohoOAuth::getClientInstance();
        $oAuthClient->generateAccessToken($this->grantToken);
    }

    public function generateAccessTokenFromRefreshToken()
    {
        $this->init();
        $oAuthClient = \ZohoOAuth::getClientInstance();
        $oAuthClient->generateAccessTokenFromRefreshToken($this->refreshToken, $this->currentUserEmail);
    }
}
