<?php

namespace App\Zoho\Api;

use Psr\SimpleCache\CacheInterface;

class ZohoApiService
{
    /**
     * @var ZohoAccessTokenService
     */
    private $zohoAccessTokenService;

    /**
     * @var string
     */
    private $apiBaseUrl = '';

    /**
     * @var FilesystemCache
     */
    private $cache;

    /**
     * @var int
     */
    private $ttl;

    public function __construct(
        ZohoAccessTokenService $zohoAccessTokenService,
        string $apiBaseUrl = '',
        CacheInterface $cache,
        int $cacheTtl = 0
    ) {
        $this->zohoAccessTokenService = $zohoAccessTokenService;
        $this->apiBaseUrl = $apiBaseUrl;

        $this->cache = $cache;
        $this->ttl = $cacheTtl;
    }

    public function init(): void
    {
        $this->zohoAccessTokenService->init();
    }

    private function request(string $slug, ?int $organizationId = null, array $filters = [])
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);

        return $ch;
    }

    public function get(string $slug, ?int $organizationId = null, array $filters = []): array
    {
        $ch = $this->request($slug, $organizationId, $filters);

        return $this->processRequest($organizationId, $ch);
    }

    // @TODO put

    public function post(string $slug, ?int $organizationId = null, array $filters = [], $data = null): array
    {
        $ch = $this->request($slug, $organizationId, $filters);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        return $this->processRequest($organizationId, $ch);
    }

    public function delete(string $slug, ?int $organizationId = null, array $filters = []): array
    {
        $ch = $this->request($slug, $organizationId, $filters);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->processRequest($organizationId, $ch, true);
    }

    /**
     * @throws \Exception
     */
    private function processRequest(?int $organizationId, $ch, bool $delete = false): array
    {
        /** @var string $result */
        $result = curl_exec($ch);
        if ($errorNumber = curl_errno($ch)) {
            if (\in_array($errorNumber, [CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED], true)) {
                curl_close($ch);
                throw new \Exception('Curl timeout in getRequest.');
            }
        }

        if ($delete or '' === $result) {
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

    /**
     * @return mixed|bool
     */
    public function getFromCache(string $key)
    {
        $item = $this->cache->get($key);
        if ($item) {
            return unserialize($item);
        }

        return false;
    }

    public function saveToCache(string $key, $values): void
    {
        $item = $this->cache->get($key);
        if (!$item) {
            $this->cache->set($key, serialize($values));
        }
    }

    public function deleteCacheByKey(string $key): void
    {
        $this->cache->delete($key);
    }
}
