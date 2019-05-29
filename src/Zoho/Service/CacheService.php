<?php

namespace App\Zoho\Service;

use Psr\SimpleCache\CacheInterface;

class CacheService
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $ttl;

    public function __construct(
        CacheInterface $cache,
        int $cacheTtl = 0
    ) {
        $this->cache = $cache;
        $this->ttl = $cacheTtl;
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
