<?php

namespace App\Service\Zoho;

class BooksWebservice extends Webservice
{
    private $urlBase = 'https://books.zoho.eu/api/v3/';

    /**
     * @var string
     */
    private $organizationId;

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
        parent::__construct(
            $clientId,
            $clientSecret,
            $redirectUri,
            $currentUserEmail,
            $logPath,
            $apiBaseUrl,
            $accountsUrl,
            $grantToken,
            $refreshToken
        );
        $this->getAccessToken();
    }

    private function getAccessToken(): void
    {
        $file = $this->logPath . '/zcrm_oauthtokens.txt';
        $fileContent = file_get_contents($file);
        $fileArray = unserialize($fileContent);
        $this->accessToken = $fileArray[0]->getAccessToken();
    }
    
    private function getRequest(string $url)
    {
        /** @var resource $ch */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Authorization: Zoho-oauthtoken '.$this->accessToken,
        ]);
        /** @var string $result */
        $result = curl_exec($ch);
        $result = json_decode($result);

        if (57 === $result->code) {
            // @TODO refresh the token..
            //$this->generateAccessToken();
            // now how do i recall this getRequest function?
            throw new \Exception('refresh the token..');
        }

        return $result;
    }

    public function getOrganizations()
    {
        $url = $this->urlBase.'organizations';

        return $this->getRequest($url);
    }

    public function getOrganizationId(): string
    {
        $organizations = $this->getOrganizations();

        return $organizations->organizations[0]->organization_id;
    }

    public function getContacts()
    {
        $this->organizationId = $this->getOrganizationId();
        $url = $this->urlBase.'contacts?organization_id='.$this->organizationId;

        return $this->getRequest($url);
    }

    public function getInvoices()
    {
        $this->organizationId = $this->getOrganizationId();
        $url = $this->urlBase.'invoices?organization_id='.$this->organizationId;

        return $this->getRequest($url);
    }
}
