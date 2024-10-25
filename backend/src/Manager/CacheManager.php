<?php

namespace App\Manager;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Class CacheManager
 *
 * Manages caching operations using a cache item pool.
 *
 * @package App\Manager
 */
class CacheManager
{
    /**
     * @var CacheItemPoolInterface
     *
     * Interface for interacting with a cache item pool.
     */
    private CacheItemPoolInterface $cacheItemPoolInterface;

    /**
     * CacheManager constructor.
     *
     * @param CacheItemPoolInterface $cacheItemPoolInterface Interface for interacting with a cache item pool.
     */
    public function __construct(CacheItemPoolInterface $cacheItemPoolInterface)
    {
        $this->cacheItemPoolInterface = $cacheItemPoolInterface;
    }

    /**
     * Checks if a cache item is present in the cache.
     *
     * @param mixed $key The cache item key.
     *
     * @return bool True if the item is cached, false otherwise.
     * @throws InvalidArgumentException
     */
    public function isCached(mixed $key): bool
    {
        try {
            return $this->cacheItemPoolInterface->getItem($key)->isHit();
        } catch (\Exception $e) {
            // TODO: Handle error
            return false;
        }
    }

    /**
     * Retrieves a value from the cache.
     *
     * @param mixed $key The cache item key.
     *
     * @return mixed The cached value or null if not found.
     * @throws InvalidArgumentException
     */
    public function getValue(mixed $key): mixed
    {
        try {
            return $this->cacheItemPoolInterface->getItem($key);
        } catch (\Exception $e) {
            // TODO: Handle error
            return null;
        }
    }

    /**
     * Sets a value in the cache.
     *
     * @param mixed $key The cache item key.
     * @param mixed $value The value to cache.
     * @param int $expiration The expiration time in seconds.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function setValue(mixed $key, mixed $value, int $expiration): void
    {
        try {
            // Set cache value data
            $cache_item = $this->cacheItemPoolInterface->getItem($key);
            $cache_item->set($value);
            $cache_item->expiresAfter($expiration);

            // Save value
            $this->cacheItemPoolInterface->save($cache_item);
        } catch (\Exception $e) {
            // TODO: Handle error
        }
    }

    /**
     * Deletes a value from the cache.
     *
     * @param mixed $key The cache item key.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function deleteValue(mixed $key): void
    {
        try {
            $this->cacheItemPoolInterface->deleteItem($key);
        } catch (\Exception $e) {
            // TODO: Handle error
        }
    }
}
