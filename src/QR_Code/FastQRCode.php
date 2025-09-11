<?php

namespace QR_Code;

use QR_Code\Cache\CacheInterface;
use QR_Code\Cache\MemoryCache;
use QR_Code\Enums\ImageEngine;
use QR_Code\Enums\ErrorCorrectionLevel;
use QR_Code\Renderer\ImageRenderer;
use QR_Code\Config\QRConfig;

/**
 * High-performance QR Code generator with caching and optimizations
 * 
 * @package QR_Code
 */
class FastQRCode
{
    private CacheInterface $cache;
    private ImageEngine $preferredEngine;
    private bool $enableCache;

    public function __construct(
        ?CacheInterface $cache = null,
        ?ImageEngine $preferredEngine = null,
        bool $enableCache = true
    ) {
        $this->cache = $cache ?? new MemoryCache();
        $this->preferredEngine = $preferredEngine ?? ImageEngine::detectBest();
        $this->enableCache = $enableCache;
    }

    /**
     * Generate QR code with optimal performance
     */
    public function generate(
        string $data,
        ?QRConfig $config = null,
        ?string $filename = null,
        string $format = 'png'
    ): string {
        $config = $config ?? QRConfig::default();
        
        // Generate cache key
        $cacheKey = $this->generateCacheKey($data, $config, $format);
        
        // Check cache first
        if ($this->enableCache && !$filename) {
            $cached = $this->cache->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        // Generate QR matrix (this is the expensive part to cache)
        $matrixCacheKey = $this->generateMatrixCacheKey($data, $config);
        $matrix = null;
        
        if ($this->enableCache) {
            $matrix = $this->cache->get($matrixCacheKey);
        }
        
        if ($matrix === null) {
            $matrix = $this->generateMatrix($data, $config);
            
            if ($this->enableCache) {
                $this->cache->set($matrixCacheKey, $matrix, 3600); // Cache matrix for 1 hour
            }
        }

        // Render using optimized renderer
        $result = $this->renderMatrix($matrix, $config, $filename, $format);
        
        // Cache result if no filename (base64 output)
        if ($this->enableCache && !$filename) {
            $this->cache->set($cacheKey, $result, 1800); // Cache for 30 minutes
        }
        
        return $result;
    }

    /**
     * Generate PNG with optimal performance
     */
    public function png(
        string $data,
        ?QRConfig $config = null,
        ?string $filename = null
    ): string {
        return $this->generate($data, $config, $filename, 'png');
    }

    /**
     * Generate SVG with optimal performance
     */
    public function svg(
        string $data,
        ?QRConfig $config = null,
        ?string $filename = null
    ): string {
        return $this->generate($data, $config, $filename, 'svg');
    }

    /**
     * Batch generate multiple QR codes efficiently
     */
    public function batch(array $items, ?QRConfig $config = null): array
    {
        $results = [];
        $config = $config ?? QRConfig::default();
        
        // Pre-warm cache and batch operations
        foreach ($items as $key => $data) {
            $results[$key] = $this->generate($data, $config);
        }
        
        return $results;
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return $this->cache->getStats();
    }

    /**
     * Clear cache
     */
    public function clearCache(): bool
    {
        return $this->cache->clear();
    }

    /**
     * Set preferred image engine
     */
    public function setImageEngine(ImageEngine $engine): self
    {
        if (!$engine->isAvailable()) {
            throw new \RuntimeException("Image engine {$engine->value} is not available");
        }
        
        $this->preferredEngine = $engine;
        return $this;
    }

    /**
     * Get available engines
     */
    public function getAvailableEngines(): array
    {
        return ImageEngine::getAvailable();
    }

    /**
     * Generate QR matrix using optimized encoding
     */
    private function generateMatrix(string $data, QRConfig $config): array
    {
        // Use the original encoder but with optimizations
        $encoder = \QR_Code\Encoder\Encoder::factory(
            $config->getLegacyErrorCorrectionLevel(),
            $config->size,
            $config->margin,
            $config->backgroundColor,
            $config->foregroundColor
        );
        
        return $encoder->encode($data);
    }

    /**
     * Render matrix using optimized renderer
     */
    private function renderMatrix(
        array $matrix,
        QRConfig $config,
        ?string $filename,
        string $format
    ): string {
        // Try preferred engine first, fallback to any available
        $engine = $this->preferredEngine;
        if (!$engine->isAvailable()) {
            $engine = ImageEngine::detectBest();
        }

        $renderer = ImageRenderer::create(
            $engine,
            $config->size,
            $config->margin,
            $config->backgroundColor,
            $config->foregroundColor
        );

        return match (strtolower($format)) {
            'png' => $renderer->renderPNG($matrix, $filename),
            'svg' => $renderer->renderSVG($matrix, $filename),
            default => throw new \InvalidArgumentException("Unsupported format: $format"),
        };
    }

    /**
     * Generate cache key for complete result
     */
    private function generateCacheKey(string $data, QRConfig $config, string $format): string
    {
        return $this->cache->generateKey($data, [
            'config' => [
                'error_level' => $config->errorCorrectionLevel->value,
                'size' => $config->size,
                'margin' => $config->margin,
                'bg_color' => $config->backgroundColor,
                'fg_color' => $config->foregroundColor,
            ],
            'format' => $format,
            'engine' => $this->preferredEngine->value
        ]);
    }

    /**
     * Generate cache key for matrix only
     */
    private function generateMatrixCacheKey(string $data, QRConfig $config): string
    {
        return $this->cache->generateKey($data, [
            'error_level' => $config->errorCorrectionLevel->value,
            'version' => $config->version,
            'matrix_only' => true
        ]);
    }
}