<?php

namespace QR_Code;

use QR_Code\Cache\CacheInterface;
use QR_Code\Cache\MemoryCache;
use QR_Code\Enums\ImageEngine;
use QR_Code\Enums\ErrorCorrectionLevel;
use QR_Code\Renderer\ImageRenderer;
use QR_Code\Config\ModernQRConfig;
use QR_Code\Performance\PerformanceMonitor;
use QR_Code\Util\ArrayOptimizer;

/**
 * Ultra-fast QR Code generator optimized for PHP 8.2+
 * 
 * @package QR_Code
 */
class UltraFastQRCode
{
    private CacheInterface $cache;
    private ImageEngine $preferredEngine;
    private bool $enableCache;
    private PerformanceMonitor $monitor;

    /**
     * Statistics - private properties with public getters for PHP 8.2 compatibility
     */
    private int $generatedCount = 0;
    private float $totalTime = 0.0;
    private int $cacheHitCount = 0;

    public function __construct(
        ?CacheInterface $cache = null,
        ?ImageEngine $preferredEngine = null,
        bool $enableCache = true,
        bool $enableMonitoring = true
    ) {
        $this->cache = $cache ?? new MemoryCache(2000); // Larger cache for PHP 8.4
        $this->preferredEngine = $preferredEngine ?? ImageEngine::detectBest();
        $this->enableCache = $enableCache;
        $this->monitor = $enableMonitoring ? new PerformanceMonitor() : null;
    }

    /**
     * Generate QR code with PHP 8.4 optimizations
     */
    public function generate(
        string $data,
        ?ModernQRConfig $config = null,
        ?string $filename = null,
        string $format = 'png'
    ): string {
        $operationId = 'generate_' . uniqid();
        $this->monitor?->startOperation($operationId);

        $config ??= ModernQRConfig::default();
        
        // Generate cache key using PHP 8.4 optimized hashing
        $cacheKey = $this->generateOptimizedCacheKey($data, $config, $format);
        
        // Check cache first
        if ($this->enableCache && !$filename) {
            $cached = $this->cache->get($cacheKey);
            if ($cached !== null) {
                $this->cacheHitCount++;
                $this->monitor?->recordCacheHit();
                $this->monitor?->endOperation($operationId);
                return $cached;
            }
            $this->monitor?->recordCacheMiss();
        }

        // Generate QR matrix with optimizations
        $matrixCacheKey = $this->generateMatrixCacheKey($data, $config);
        $matrix = null;
        
        if ($this->enableCache) {
            $matrix = $this->cache->get($matrixCacheKey);
        }
        
        if ($matrix === null) {
            $matrix = $this->generateOptimizedMatrix($data, $config);
            
            if ($this->enableCache) {
                // Compress matrix for better cache efficiency
                $compressed = ArrayOptimizer::compressMatrix($matrix);
                $this->cache->set($matrixCacheKey, $compressed, 3600);
            }
        } else {
            // Decompress cached matrix if needed
            if (is_string($matrix) && $this->enableCache) {
                $size = $this->calculateMatrixSize($data, $config);
                $matrix = ArrayOptimizer::decompressMatrix($matrix, $size);
            }
        }

        // Render using optimized renderer
        $result = $this->renderMatrix($matrix, $config, $filename, $format);
        
        // Cache result if no filename
        if ($this->enableCache && !$filename) {
            $this->cache->set($cacheKey, $result, 1800);
        }
        
        // Update statistics using asymmetric visibility
        $this->generatedCount++;
        $duration = $this->monitor?->endOperation($operationId) ?? 0.0;
        $this->totalTime += $duration;
        
        return $result;
    }

    /**
     * Batch generate with PHP 8.4 parallel processing optimizations
     */
    public function batchGenerate(array $items, ?ModernQRConfig $config = null): array
    {
        $config ??= ModernQRConfig::default();
        
        // Use PHP 8.4 array optimizations for batch processing
        return ArrayOptimizer::batchProcess($items, function($data) use ($config) {
            return $this->generate($data, $config);
        });
    }

    /**
     * Smart cache warming using PHP 8.4 features
     */
    public function warmCache(array $data, ?ModernQRConfig $config = null): int
    {
        $config ??= ModernQRConfig::default();
        $warmed = 0;
        
        foreach ($data as $item) {
            $cacheKey = $this->generateOptimizedCacheKey($item, $config, 'png');
            
            if (!$this->cache->has($cacheKey)) {
                $this->generate($item, $config);
                $warmed++;
            }
        }
        
        return $warmed;
    }

    /**
     * Get generated count
     */
    public function getGeneratedCount(): int
    {
        return $this->generatedCount;
    }

    /**
     * Get total time
     */
    public function getTotalTime(): float
    {
        return $this->totalTime;
    }

    /**
     * Get cache hit count
     */
    public function getCacheHitCount(): int
    {
        return $this->cacheHitCount;
    }

    /**
     * Get average generation time
     */
    public function getAverageTime(): float
    {
        return $this->generatedCount > 0 
            ? $this->totalTime / $this->generatedCount 
            : 0.0;
    }

    /**
     * Get cache hit ratio
     */
    public function getCacheHitRatio(): float
    {
        return $this->generatedCount > 0 
            ? $this->cacheHitCount / $this->generatedCount 
            : 0.0;
    }

    /**
     * Get comprehensive performance statistics
     */
    public function getPerformanceStats(): array
    {
        $stats = [
            'generated_count' => $this->getGeneratedCount(),
            'total_time' => $this->getTotalTime(),
            'average_time' => $this->getAverageTime(),
            'cache_hit_count' => $this->getCacheHitCount(),
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'preferred_engine' => $this->preferredEngine->value,
            'cache_enabled' => $this->enableCache,
        ];

        if ($this->monitor) {
            $stats = array_merge($stats, $this->monitor->getSummary());
        }

        return $stats;
    }

    /**
     * Optimized cache key generation using PHP 8.4 features
     */
    private function generateOptimizedCacheKey(string $data, ModernQRConfig $config, string $format): string
    {
        // Use optimized hash performance
        $keyData = [
            'd' => hash('sha256', $data),
            'ec' => $config->getErrorCorrectionLevel()->value,
            's' => $config->getSize(),
            'm' => $config->getMargin(),
            'bg' => $config->getBackgroundColor(),
            'fg' => $config->getForegroundColor(),
            'f' => $format,
            'e' => $this->preferredEngine->value
        ];
        
        return 'qr82_' . hash('sha256', serialize($keyData));
    }

    /**
     * Generate matrix cache key
     */
    private function generateMatrixCacheKey(string $data, ModernQRConfig $config): string
    {
        $keyData = [
            'd' => hash('sha256', $data),
            'ec' => $config->getErrorCorrectionLevel()->value,
            'v' => $config->getVersion(),
        ];
        
        return 'mat82_' . hash('sha256', serialize($keyData));
    }

    /**
     * Generate optimized matrix using PHP 8.4 features
     */
    private function generateOptimizedMatrix(string $data, ModernQRConfig $config): array
    {
        $encoder = \QR_Code\Encoder\Encoder::factory(
            $config->getLegacyErrorCorrectionLevel(),
            $config->getSize(),
            $config->getMargin(),
            $config->getBackgroundColor(),
            $config->getForegroundColor()
        );
        
        $matrix = $encoder->encode($data);
        
        // Optimize matrix using PHP 8.4 array functions
        return ArrayOptimizer::optimizeMatrix($matrix);
    }

    /**
     * Calculate matrix size for decompression
     */
    private function calculateMatrixSize(string $data, ModernQRConfig $config): int
    {
        // Estimate size based on data length and error correction
        $dataLength = strlen($data);
        $baseSizes = [21, 25, 29, 33, 37, 41, 45, 49, 53, 57];
        
        $estimatedIndex = min(
            floor($dataLength / 10) + $config->getErrorCorrectionLevel()->value,
            count($baseSizes) - 1
        );
        
        return $baseSizes[$estimatedIndex] ?? 57;
    }

    /**
     * Render matrix using optimized renderer
     */
    private function renderMatrix(
        array $matrix,
        ModernQRConfig $config,
        ?string $filename,
        string $format
    ): string {
        // Validate matrix before rendering
        if (!ArrayOptimizer::validateMatrix($matrix)) {
            throw new \InvalidArgumentException('Invalid QR code matrix');
        }

        $engine = $this->preferredEngine->isAvailable() 
            ? $this->preferredEngine 
            : ImageEngine::detectBest();

        $renderer = ImageRenderer::create(
            $engine,
            $config->getSize(),
            $config->getMargin(),
            $config->getBackgroundColor(),
            $config->getForegroundColor()
        );

        return match (strtolower($format)) {
            'png' => $renderer->renderPNG($matrix, $filename),
            'svg' => $renderer->renderSVG($matrix, $filename),
            default => throw new \InvalidArgumentException("Unsupported format: $format"),
        };
    }

    /**
     * Reset all statistics
     */
    public function resetStats(): void
    {
        $this->generatedCount = 0;
        $this->totalTime = 0.0;
        $this->cacheHitCount = 0;
        $this->monitor?->reset();
    }
}