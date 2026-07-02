<?php

namespace QR_Code\Cache;

/**
 * Ultra-fast in-memory cache for QR codes
 */
class MemoryCache implements CacheInterface
{
    private array $cache = [];

    private readonly int $maxEntries;

    private readonly int $defaultTtl;

    public function __construct(int $maxEntries = 1000, int $defaultTtl = 3600)
    {
        $this->maxEntries = $maxEntries;
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * Get cached item with automatic expiration check
     */
    public function get(string $key): mixed
    {
        if (! isset($this->cache[$key])) {
            return null;
        }

        $cached = $this->cache[$key];

        // Check expiration
        if ($cached['expires'] > 0 && $cached['expires'] < time()) {
            unset($this->cache[$key]);

            return null;
        }

        // Update access time for LRU
        $this->cache[$key]['accessed'] = time();

        return $cached['data'];
    }

    /**
     * Store item with TTL and LRU eviction
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;

        // Evict if cache is full
        if (count($this->cache) >= $this->maxEntries && ! isset($this->cache[$key])) {
            $this->evictLRU();
        }

        $this->cache[$key] = [
            'data' => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,
            'created' => time(),
            'accessed' => time(),
        ];

        return true;
    }

    /**
     * Check if key exists and is not expired
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Delete cached item
     */
    public function delete(string $key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    /**
     * Clear all cache
     */
    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    /**
     * Generate cache key for QR code
     */
    public function generateKey(string $data, array $options = []): string
    {
        // Create deterministic key based on content and options
        $keyData = [
            'data' => $data,
            'options' => $options,
        ];

        return 'qr_'.hash('xxh3', serialize($keyData));
    }

    /**
     * Clean up expired entries
     */
    public function cleanup(): int
    {
        $deleted = 0;
        $now = time();

        foreach ($this->cache as $key => $cached) {
            if ($cached['expires'] > 0 && $cached['expires'] < $now) {
                unset($this->cache[$key]);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        $now = time();
        $expired = 0;
        $totalSize = 0;

        foreach ($this->cache as $cached) {
            if ($cached['expires'] > 0 && $cached['expires'] < $now) {
                $expired++;
            }
            $totalSize += strlen(serialize($cached['data']));
        }

        return [
            'total_entries' => count($this->cache),
            'expired_entries' => $expired,
            'max_entries' => $this->maxEntries,
            'estimated_size' => $totalSize,
            'memory_usage' => memory_get_usage(true),
        ];
    }

    /**
     * Evict least recently used entry
     */
    private function evictLRU(): void
    {
        if (empty($this->cache)) {
            return;
        }

        $oldestKey = null;
        $oldestTime = PHP_INT_MAX;

        foreach ($this->cache as $key => $cached) {
            if ($cached['accessed'] < $oldestTime) {
                $oldestTime = $cached['accessed'];
                $oldestKey = $key;
            }
        }

        if ($oldestKey !== null) {
            unset($this->cache[$oldestKey]);
        }
    }
}
