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
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ZohoAccessTokenService $zohoAccessTokenService,
        $apiBaseUrl,
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

    public function getRequest(string $urlPart): \stdClass
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);

        /** @var string $result */
        $result = curl_exec($ch);
        if ($errorNumber = curl_errno($ch)) {
            if (\in_array($errorNumber, [CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED], true)) {
                curl_close($ch);
                throw new \Exception($this->translator->trans('get_request.timeout', [], 'exceptions'));
            }
        }

        try {
            $result = json_decode($result);
        } catch (\Exception $e) {
            curl_close($ch);
            throw new \Exception(
                $this->translator->trans(
                    'get_request.json_decode %msg%',
                    ['%msg%' => json_last_error_msg()],
                    'exceptions'
                )
            );
        }

        if (57 === $result->code) {
            curl_close($ch);
            throw new \Exception($this->translator->trans('get_request.refresh', [], 'exceptions'));
        } elseif (0 !== $result->code) {
            curl_close($ch);
            throw new \Exception($this->translator->trans('get_request.error_in_code', [], 'exceptions'));
        }

        return $result;
    }
}
