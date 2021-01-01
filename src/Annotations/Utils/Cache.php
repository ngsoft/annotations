<?php

declare(strict_types=1);

namespace NGSOFT\Annotations\Utils;

use Psr\Cache\{
    CacheItemInterface, CacheItemPoolInterface
};

/**
 * Decorator for Real Cache
 */
class Cache implements CacheItemPoolInterface {

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var int */
    private $ttl = 24 * 60 * 60;

    /**
     * @param CacheItemPoolInterface $cache
     * @param int|null $ttl
     */
    public function __construct(
            CacheItemPoolInterface $cache,
            ?int $ttl = null
    ) {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /** {@inheritdoc} */
    public function clear() {
        return $this->cache->clear();
    }

    /** {@inheritdoc} */
    public function commit() {
        return $this->cache->commit();
    }

    /** {@inheritdoc} */
    public function deleteItem($key) {
        return $this->cache->deleteItem($key);
    }

    /** {@inheritdoc} */
    public function deleteItems(array $keys) {
        return $this->cache->deleteItems($keys);
    }

    /** {@inheritdoc} */
    public function getItem($key) {
        return $this->cache->getItem($key);
    }

    /** {@inheritdoc} */
    public function getItems(array $keys = []) {
        return $this->cache->getItems($keys);
    }

    /** {@inheritdoc} */
    public function hasItem($key) {
        return $this->cache->hasItem($key);
    }

    /** {@inheritdoc} */
    public function save(CacheItemInterface $item) {
        $item->expiresAfter($this->ttl);
        return $this->cache->save($item);
    }

    /** {@inheritdoc} */
    public function saveDeferred(CacheItemInterface $item) {
        $item->expiresAfter($this->ttl);
        return $this->cache->saveDeferred($item);
    }

}
