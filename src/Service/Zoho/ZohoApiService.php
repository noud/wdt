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
        $apiBaseUrl
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
                throw new \Exception('timeout..in getRequest..');
            }
        }

        try {
            $result = json_decode($result);
        } catch (\Exception $e) {
            curl_close($ch);
            throw new \Exception('json decode catch error..in getRequest.. '.json_last_error_msg());
        }

        if (57 === $result->code) {
            // @TODO check refresh the token..
            //$this->apiService->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
            // @TODO now i should re-call this method..
            //$this->getRequest($url);
            // @TODO and i should keep a timer about how many times..
            // now how do i recall this getRequest function?
            curl_close($ch);
            throw new \Exception('refresh the token..in getRequest..');
        } elseif (0 !== $result->code) {
            curl_close($ch);
            throw new \Exception('Error occurred..in getRequest..');
        }

        return $result;
    }
}
