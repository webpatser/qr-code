<?php

namespace QR_Code\Enums;

/**
 * Image generation engines with performance characteristics
 */
enum ImageEngine: string
{
    case GD = 'gd';
    case Imagick = 'imagick';
    case None = 'none';  // For text-only output

    /**
     * Check if the engine is available
     */
    public function isAvailable(): bool
    {
        return match ($this) {
            self::GD => extension_loaded('gd'),
            self::Imagick => extension_loaded('imagick'),
            self::None => true,
        };
    }

    /**
     * Get performance score (higher is better)
     */
    public function getPerformanceScore(): int
    {
        return match ($this) {
            self::Imagick => 100,  // Fastest
            self::GD => 75,       // Good
            self::None => 50,     // No image generation
        };
    }

    /**
     * Get supported formats for this engine
     */
    public function getSupportedFormats(): array
    {
        return match ($this) {
            self::GD => ['png', 'jpeg', 'gif', 'webp'],
            self::Imagick => ['png', 'jpeg', 'gif', 'webp', 'svg', 'pdf', 'tiff'],
            self::None => ['text', 'raw'],
        };
    }

    /**
     * Automatically detect the best available engine
     */
    public static function detectBest(): self
    {
        $engines = [self::Imagick, self::GD, self::None];

        foreach ($engines as $engine) {
            if ($engine->isAvailable()) {
                return $engine;
            }
        }

        return self::None;
    }

    /**
     * Get all available engines sorted by performance
     */
    public static function getAvailable(): array
    {
        $engines = [self::Imagick, self::GD, self::None];

        return array_filter($engines, fn ($engine) => $engine->isAvailable());
    }
}
