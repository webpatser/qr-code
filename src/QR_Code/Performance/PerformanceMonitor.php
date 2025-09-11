<?php

namespace QR_Code\Performance;

/**
 * Performance monitor using PHP 8.4 asymmetric visibility
 */
class PerformanceMonitor
{
    /**
     * Total operations count - publicly readable, privately writable
     */
    public private(set) int $totalOperations = 0;

    /**
     * Total time spent - publicly readable, privately writable
     */
    public private(set) float $totalTime = 0.0;

    /**
     * Cache hits - publicly readable, privately writable
     */
    public private(set) int $cacheHits = 0;

    /**
     * Cache misses - publicly readable, privately writable
     */
    public private(set) int $cacheMisses = 0;

    /**
     * Memory usage peak - publicly readable, privately writable
     */
    public private(set) int $peakMemoryUsage = 0;

    /**
     * Current active operations - privately managed
     */
    private array $activeOperations = [];

    /**
     * Start timing an operation
     */
    public function startOperation(string $operationId): void
    {
        $this->activeOperations[$operationId] = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
        ];
    }

    /**
     * End timing an operation and record metrics
     */
    public function endOperation(string $operationId): float
    {
        if (! isset($this->activeOperations[$operationId])) {
            throw new \InvalidArgumentException("Operation {$operationId} was not started");
        }

        $operation = $this->activeOperations[$operationId];
        $duration = microtime(true) - $operation['start_time'];
        $currentMemory = memory_get_usage(true);

        // Update metrics using asymmetric visibility
        $this->totalOperations++;
        $this->totalTime += $duration;
        $this->peakMemoryUsage = max($this->peakMemoryUsage, $currentMemory);

        unset($this->activeOperations[$operationId]);

        return $duration;
    }

    /**
     * Record a cache hit
     */
    public function recordCacheHit(): void
    {
        $this->cacheHits++;
    }

    /**
     * Record a cache miss
     */
    public function recordCacheMiss(): void
    {
        $this->cacheMisses++;
    }

    /**
     * Get average operation time (computed property)
     */
    public function getAverageTime(): float
    {
        return $this->totalOperations > 0
            ? $this->totalTime / $this->totalOperations
            : 0.0;
    }

    /**
     * Get cache hit ratio (computed property)
     */
    public function getCacheHitRatio(): float
    {
        $total = $this->cacheHits + $this->cacheMisses;

        return $total > 0 ? $this->cacheHits / $total : 0.0;
    }

    /**
     * Get performance summary
     */
    public function getSummary(): array
    {
        return [
            'total_operations' => $this->totalOperations,
            'total_time' => $this->totalTime,
            'average_time' => $this->getAverageTime(),
            'cache_hits' => $this->cacheHits,
            'cache_misses' => $this->cacheMisses,
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'peak_memory_usage' => $this->peakMemoryUsage,
            'active_operations' => count($this->activeOperations),
        ];
    }

    /**
     * Reset all metrics
     */
    public function reset(): void
    {
        $this->totalOperations = 0;
        $this->totalTime = 0.0;
        $this->cacheHits = 0;
        $this->cacheMisses = 0;
        $this->peakMemoryUsage = 0;
        $this->activeOperations = [];
    }
}
