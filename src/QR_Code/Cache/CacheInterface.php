<?php

namespace QR_Code\Cache;

/**
 * Cache interface for QR code data and images
 */
interface CacheInterface
{
    /**
     * Get cached item by key
     */
    public function get(string $key): mixed;

    /**
     * Store item in cache
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    /**
     * Check if key exists in cache
     */
    public function has(string $key): bool;

    /**
     * Delete item from cache
     */
    public function delete(string $key): bool;

    /**
     * Clear all cache
     */
    public function clear(): bool;

    /**
     * Generate cache key for QR code data
     */
    public function generateKey(string $data, array $options = []): string;
}
