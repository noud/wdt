<?php

namespace App\Service\Zoho;

class ZohoApiService
{
    /**
     * @var ZohoAccessTokenService
     */
    private $zohoAccessTokenService;

    /**
     * @var string
     */
    private $apiBaseUrl;

    public function __construct(
        ZohoAccessTokenService $zohoAccessTokenService,
        string $apiBaseUrl
    ) {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function init(): void
    {
        $this->zohoAccessTokenService->init();
    }

    public function getRequest(string $urlPart)
    {
        $url = $this->apiBaseUrl.$urlPart;

        $this->zohoAccessTokenService->setAccessToken();
        $accessTokenExpiryTime = $this->zohoAccessTokenService->getAccessTokenExpiryTime();
        if ($accessTokenExpiryTime < round(microtime(true) * 1000)) {
            $this->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
        }

        /** @var resource $ch */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
            'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds

        /** @var string $result */
        $result = curl_exec($ch);
        if ($errorNumber = curl_errno($ch)) {
            if (\in_array($errorNumber, [CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED], true)) {
                curl_close($ch);
                throw new \Exception('Curl timeout in getRequest.');
            }
        }

        try {
            $result = json_decode($result);
        } catch (\Exception $e) {
            curl_close($ch);
            throw new \Exception('Json decode error in getRequest. '.json_last_error_msg());
        }

        if (57 === $result->code) {
            // this should not happen
            curl_close($ch);
            throw new \Exception('Token is not valid anymore and needs to be refreshed in getRequest.');
        } elseif (0 !== $result->code) {
            curl_close($ch);
            throw new \Exception('General error occurre in getRequest.');
        }

        return $result;
    }
}
