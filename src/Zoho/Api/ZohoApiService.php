<?php

namespace App\Zoho\Api;

use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ZohoAccessTokenService $zohoAccessTokenService,
        string $apiBaseUrl,
        TranslatorInterface $translator
    ) {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->zohoAccessTokenService->init();
    }

    public function setService(string $slug, array $filters = [])
    {
        $this->apiUrl = $this->apiBaseUrl.$slug.'?'.http_build_query($filters);
    }

    /**
     * @throws \Exception
     */
    public function getRequest(?int $orgId = null, $data = null): array
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
        dump($this->apiUrl);
        dump($orgId);
        /** @var resource $ch */
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);

        /** @var string $result */
        $result = curl_exec($ch);
        if ($errorNumber = curl_errno($ch)) {
            if (\in_array($errorNumber, [CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED], true)) {
                curl_close($ch);
                throw new \Exception($this->translator->trans('get_request.timeout', [], 'exceptions'));
            }
        }

        return $this->processResult($result, $orgId, $ch);
    }

    private function processResult(string $result, ?int $orgId, $ch): array
    {
        dump($result);
        $result = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            curl_close($ch);
            throw new \Exception(
                $this->translator->trans(
                    'get_request.json_decode %msg%',
                    ['%msg%' => json_last_error_msg()],
                    'exceptions'
                )
            );
        }

        if (!$orgId && isset($result->code) && 57 === $result->code) {
            // this should not happen
            curl_close($ch);
            $this->zohoAccessTokenService->generateAccessTokenFromRefreshToken();
            throw new \Exception($this->translator->trans('get_request.refresh', [], 'exceptions'));
        } elseif (!$orgId && isset($result->code) && 0 !== $result->code) {
            curl_close($ch);
            throw new \Exception($this->translator->trans('get_request.error_in_code', [], 'exceptions'));
        }

        return $result;
    }
}
