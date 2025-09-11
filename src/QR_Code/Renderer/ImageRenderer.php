<?php

namespace QR_Code\Renderer;

use QR_Code\Enums\ImageEngine;

/**
 * High-performance image renderer supporting multiple engines
 * 
 * @package QR_Code\Renderer
 */
abstract class ImageRenderer
{
    protected ImageEngine $engine;
    protected int $size;
    protected int $margin;
    protected int $backgroundColor;
    protected int $foregroundColor;

    public function __construct(
        ImageEngine $engine,
        int $size = 3,
        int $margin = 4,
        int $backgroundColor = QR_WHITE,
        int $foregroundColor = QR_BLACK
    ) {
        if (!$engine->isAvailable()) {
            throw new \RuntimeException("Image engine {$engine->value} is not available");
        }

        $this->engine = $engine;
        $this->size = $size;
        $this->margin = $margin;
        $this->backgroundColor = $backgroundColor;
        $this->foregroundColor = $foregroundColor;
    }

    /**
     * Create renderer for the best available engine
     */
    public static function createBest(
        int $size = 3,
        int $margin = 4,
        int $backgroundColor = QR_WHITE,
        int $foregroundColor = QR_BLACK
    ): self {
        $engine = ImageEngine::detectBest();
        
        return match ($engine) {
            ImageEngine::Imagick => new ImagickRenderer($engine, $size, $margin, $backgroundColor, $foregroundColor),
            ImageEngine::GD => new GdRenderer($engine, $size, $margin, $backgroundColor, $foregroundColor),
            ImageEngine::None => throw new \RuntimeException('No image engine available'),
        };
    }

    /**
     * Create renderer for specific engine
     */
    public static function create(
        ImageEngine $engine,
        int $size = 3,
        int $margin = 4,
        int $backgroundColor = QR_WHITE,
        int $foregroundColor = QR_BLACK
    ): self {
        return match ($engine) {
            ImageEngine::Imagick => new ImagickRenderer($engine, $size, $margin, $backgroundColor, $foregroundColor),
            ImageEngine::GD => new GdRenderer($engine, $size, $margin, $backgroundColor, $foregroundColor),
            ImageEngine::None => throw new \RuntimeException('None engine cannot render images'),
        };
    }

    /**
     * Render QR code matrix to PNG
     */
    abstract public function renderPNG(array $matrix, ?string $filename = null): string;

    /**
     * Render QR code matrix to SVG
     */
    abstract public function renderSVG(array $matrix, ?string $filename = null): string;

    /**
     * Get engine information
     */
    public function getEngine(): ImageEngine
    {
        return $this->engine;
    }

    /**
     * Calculate total image dimensions
     */
    protected function calculateDimensions(array $matrix): array
    {
        $moduleCount = count($matrix);
        $totalSize = ($moduleCount + 2 * $this->margin) * $this->size;
        
        return [
            'moduleCount' => $moduleCount,
            'totalSize' => $totalSize,
            'moduleSize' => $this->size,
            'margin' => $this->margin * $this->size
        ];
    }

    /**
     * Convert color integer to RGB array
     */
    protected function colorToRGB(int $color): array
    {
        return [
            'r' => ($color >> 16) & 0xFF,
            'g' => ($color >> 8) & 0xFF,
            'b' => $color & 0xFF
        ];
    }
}