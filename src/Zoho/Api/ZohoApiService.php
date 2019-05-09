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

    /**
     * @throws \Exception
     */
    public function get(string $slug, ?int $organizationId = null, array $filters = [], $data = null, bool $delete = false): array
    {
        $this->zohoAccessTokenService->checkAccessTokenExpiryTime();

        $url = $this->apiBaseUrl.$slug.'?'.http_build_query($filters);

        if ($organizationId) {
            $header = [
                'orgId: '.$organizationId,
                'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
            ];
        } else {
            $header = [
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization: Zoho-oauthtoken '.$this->zohoAccessTokenService->getAccessToken(),
            ];
        }

        /** @var resource $ch */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if ($delete) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);

        /** @var string $result */
        $result = curl_exec($ch);
        if ($errorNumber = curl_errno($ch)) {
            if (\in_array($errorNumber, [CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED], true)) {
                curl_close($ch);
                throw new \Exception('Curl timeout in getRequest.');
            }
        }

        if ($delete or '' == $result) {
            return [];
        }

        return $this->processResult($result, $organizationId, $ch);
    }

    private function processResult(string $result, ?int $organizationId, $ch): array
    {
        if ('Internal Server Error' !== $result && '\n' !== $result) {
            $result = $this->decodeResult($result, $ch);

            if (!$organizationId && isset($result->code) && 57 === $result->code) {
                // this should not happen
                curl_close($ch);
                $this->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
                throw new \Exception('Token is not valid anymore and needs to be refreshed in getRequest');
            } elseif (!$organizationId && isset($result->code) && 0 !== $result->code) {
                curl_close($ch);
                throw new \Exception('General error occured in getRequest.');
            }

            return $result;
        }
        curl_close($ch);
        throw new \Exception('Internal Server Error in getRequest.');
    }

    private function decodeResult(string $result, $ch): array
    {
        $result = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            curl_close($ch);
            throw new \Exception(sprintf('Json decode error in getRequest: %s.', json_last_error_msg()));
        }

        return $result;
    }
}
