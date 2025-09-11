<?php

namespace QR_Code\Renderer;

use QR_Code\Enums\ImageEngine;

/**
 * Optimized GD renderer with performance improvements
 * 
 * @package QR_Code\Renderer
 */
class GdRenderer extends ImageRenderer
{
    /**
     * Render QR code matrix to PNG using optimized GD
     */
    public function renderPNG(array $matrix, ?string $filename = null): string
    {
        $dimensions = $this->calculateDimensions($matrix);
        $totalSize = $dimensions['totalSize'];
        $moduleSize = $dimensions['moduleSize'];
        $margin = $dimensions['margin'];

        // Create image with true color for better performance
        $image = imagecreatetruecolor($totalSize, $totalSize);
        if (!$image) {
            throw new \RuntimeException('Failed to create GD image');
        }

        // Allocate colors
        $bgRGB = $this->colorToRGB($this->backgroundColor);
        $fgRGB = $this->colorToRGB($this->foregroundColor);
        
        $bgColor = imagecolorallocate($image, $bgRGB['r'], $bgRGB['g'], $bgRGB['b']);
        $fgColor = imagecolorallocate($image, $fgRGB['r'], $fgRGB['g'], $fgRGB['b']);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Optimized module drawing using imagefilledrectangle for larger modules
        if ($moduleSize === 1) {
            // For size 1, use imagesetpixel for best performance
            foreach ($matrix as $row => $line) {
                // Handle both string and array formats
                $lineData = is_string($line) ? str_split($line) : $line;
                foreach ($lineData as $col => $module) {
                    if ((is_string($module) && $module === '1') || (is_numeric($module) && ($module & 1))) {
                        imagesetpixel(
                            $image,
                            $margin + $col,
                            $margin + $row,
                            $fgColor
                        );
                    }
                }
            }
        } else {
            // For larger sizes, use imagefilledrectangle
            foreach ($matrix as $row => $line) {
                // Handle both string and array formats
                $lineData = is_string($line) ? str_split($line) : $line;
                foreach ($lineData as $col => $module) {
                    if ((is_string($module) && $module === '1') || (is_numeric($module) && ($module & 1))) {
                        $x1 = $margin + $col * $moduleSize;
                        $y1 = $margin + $row * $moduleSize;
                        $x2 = $x1 + $moduleSize - 1;
                        $y2 = $y1 + $moduleSize - 1;
                        
                        imagefilledrectangle($image, $x1, $y1, $x2, $y2, $fgColor);
                    }
                }
            }
        }

        // Output handling
        if ($filename !== null) {
            $success = imagepng($image, $filename, 6); // Compression level 6 for good balance
            if (!$success) {
                imagedestroy($image);
                throw new \RuntimeException('Failed to save PNG image');
            }
            $result = $filename;
        } else {
            ob_start();
            imagepng($image, null, 6);
            $imageData = ob_get_clean();
            $result = 'data:image/png;base64,' . base64_encode($imageData);
        }

        imagedestroy($image);
        return $result;
    }

    /**
     * Render QR code matrix to SVG (same as ImagickRenderer for consistency)
     */
    public function renderSVG(array $matrix, ?string $filename = null): string
    {
        $dimensions = $this->calculateDimensions($matrix);
        $totalSize = $dimensions['totalSize'];
        $moduleSize = $dimensions['moduleSize'];
        $margin = $dimensions['margin'];

        $bgColor = $this->colorToHex($this->backgroundColor);
        $fgColor = $this->colorToHex($this->foregroundColor);

        // Build SVG with efficient string concatenation
        $svg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $svg .= "<svg width=\"{$totalSize}\" height=\"{$totalSize}\" xmlns=\"http://www.w3.org/2000/svg\">\n";
        $svg .= "<rect width=\"{$totalSize}\" height=\"{$totalSize}\" fill=\"{$bgColor}\"/>\n";

        // Collect all rectangles for batch processing
        $rects = [];
        foreach ($matrix as $row => $line) {
            foreach ($line as $col => $module) {
                if ($module & 1) { // Black module
                    $x = $margin + $col * $moduleSize;
                    $y = $margin + $row * $moduleSize;
                    $rects[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$moduleSize}\" height=\"{$moduleSize}\" fill=\"{$fgColor}\"/>";
                }
            }
        }

        $svg .= implode("\n", $rects) . "\n</svg>";

        if ($filename !== null) {
            file_put_contents($filename, $svg);
            return $filename;
        }

        return $svg;
    }

    /**
     * Convert color integer to hex string
     */
    private function colorToHex(int $color): string
    {
        return sprintf('#%06X', $color);
    }
}