<?php

namespace QR_Code\Cache;

/**
 * High-performance file-based cache for QR codes
 */
class FileCache implements CacheInterface
{
    private readonly string $cacheDir;

    private readonly int $defaultTtl;

    public function __construct(?string $cacheDir = null, int $defaultTtl = 3600)
    {
        $this->cacheDir = $cacheDir ?? sys_get_temp_dir().'/qrcode_cache';
        $this->defaultTtl = $defaultTtl;

        if (! is_dir($this->cacheDir)) {
            if (! mkdir($this->cacheDir, 0755, true)) {
                throw new \RuntimeException("Cannot create cache directory: {$this->cacheDir}");
            }
        }
    }

    /**
     * Get cached item with automatic expiration check
     */
    public function get(string $key): mixed
    {
        $file = $this->getFilePath($key);

        if (! file_exists($file)) {
            return null;
        }

        $data = file_get_contents($file);
        if ($data === false) {
            return null;
        }

        $cached = unserialize($data, ['allowed_classes' => false]);

        // Check expiration
        if ($cached['expires'] > 0 && $cached['expires'] < time()) {
            $this->delete($key);

            return null;
        }

        return $cached['data'];
    }

    /**
     * Store item with TTL
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $file = $this->getFilePath($key);

        $cached = [
            'data' => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,
            'created' => time(),
        ];

        $result = file_put_contents($file, serialize($cached), LOCK_EX);

        return $result !== false;
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
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    /**
     * Clear all cache files
     */
    public function clear(): bool
    {
        $files = glob($this->cacheDir.'/*.cache');
        $success = true;

        foreach ($files as $file) {
            if (! unlink($file)) {
                $success = false;
            }
        }

        return $success;
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
     * Clean up expired cache files
     */
    public function cleanup(): int
    {
        $files = glob($this->cacheDir.'/*.cache');
        $deleted = 0;

        foreach ($files as $file) {
            $data = file_get_contents($file);
            if ($data === false) {
                continue;
            }

            $cached = unserialize($data, ['allowed_classes' => false]);

            // Delete if expired
            if ($cached['expires'] > 0 && $cached['expires'] < time()) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        $files = glob($this->cacheDir.'/*.cache');
        $totalSize = 0;
        $expired = 0;
        $valid = 0;

        foreach ($files as $file) {
            $totalSize += filesize($file);

            $data = file_get_contents($file);
            if ($data === false) {
                continue;
            }

            $cached = unserialize($data, ['allowed_classes' => false]);

            if ($cached['expires'] > 0 && $cached['expires'] < time()) {
                $expired++;
            } else {
                $valid++;
            }
        }

        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'valid_entries' => $valid,
            'expired_entries' => $expired,
            'cache_dir' => $this->cacheDir,
        ];
    }

    /**
     * Get file path for cache key
     */
    private function getFilePath(string $key): string
    {
        return $this->cacheDir.'/'.preg_replace('/[^a-zA-Z0-9_-]/', '', $key).'.cache';
    }
}
