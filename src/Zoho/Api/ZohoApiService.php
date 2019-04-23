<?php

namespace App\Zoho\Api;

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

    /**
     * @var string
     */
    private $apiUrl;

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

    public function setService(string $slug, array $filters = [])
    {
        $httpQuery = \count($filters) ? '?'.http_build_query($filters) : '';
        $this->apiUrl = $this->apiBaseUrl.$slug.$httpQuery;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * @throws \Exception
     */
    public function getRequest($orgId = null, $data = null, $files = null)
    {
        $this->zohoAccessTokenService->setAccessToken();
        $accessTokenExpiryTime = $this->zohoAccessTokenService->getAccessTokenExpiryTime();
        if ($accessTokenExpiryTime < round(microtime(true) * 1000)) {
            $this->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
        }

        if ($orgId) {
            $header = [
                'orgId: '.$orgId,
                'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
            ];
        } else {
            $header = [
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
            ];
        }

        /** @var resource $ch */
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data), $files);
        }
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

        if ('Internal Server Error' !== $result && '\n' !== $result) {
            try {
                $result = json_decode($result, true);
            } catch (\Exception $e) {
                curl_close($ch);
                throw new \Exception('json decode catch error..in getRequest.. '.json_last_error_msg());
            }

            if (!$orgId && 57 === $result['code']) {
                // this should not happen
                curl_close($ch);
                $this->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
                throw new \Exception('refresh the token..in getRequest..');
            } elseif (!$orgId && 0 !== $result['code']) {
                curl_close($ch);
                throw new \Exception('Error occurred..in getRequest..');
            }
        } else {
            curl_close($ch);
            throw new \Exception('Internal Server Error in getRequest.');
        }

        return $result;
    }
}
