<?php

namespace QR_Code\Renderer;

/**
 * High-performance ImageMagick renderer
 */
class ImagickRenderer extends ImageRenderer
{
    /**
     * Render QR code matrix to PNG using ImageMagick
     */
    public function renderPNG(array $matrix, ?string $filename = null): string
    {
        $dimensions = $this->calculateDimensions($matrix);
        $totalSize = $dimensions['totalSize'];
        $moduleSize = $dimensions['moduleSize'];
        $margin = $dimensions['margin'];

        // Create ImageMagick canvas
        $imagick = new \Imagick;
        $bgColor = $this->colorToHex($this->backgroundColor);
        $imagick->newImage($totalSize, $totalSize, $bgColor);
        $imagick->setImageFormat('png');

        // Create drawing object for efficient batch operations
        $draw = new \ImagickDraw;
        $draw->setFillColor($this->colorToHex($this->foregroundColor));
        $draw->setStrokeColor('none');

        // Batch draw all black modules for better performance
        $rectangles = [];
        foreach ($matrix as $row => $line) {
            // Handle both string and array formats
            $lineData = is_string($line) ? str_split($line) : $line;
            foreach ($lineData as $col => $module) {
                if ((is_string($module) && $module === '1') || (is_numeric($module) && ($module & 1))) {
                    $x = $margin + $col * $moduleSize;
                    $y = $margin + $row * $moduleSize;
                    $rectangles[] = [$x, $y, $x + $moduleSize - 1, $y + $moduleSize - 1];
                }
            }
        }

        // Draw all rectangles in one operation for performance
        foreach ($rectangles as [$x1, $y1, $x2, $y2]) {
            $draw->rectangle($x1, $y1, $x2, $y2);
        }

        $imagick->drawImage($draw);

        // Output handling
        if ($filename !== null) {
            $imagick->writeImage($filename);
            $result = $filename;
        } else {
            $result = 'data:image/png;base64,'.base64_encode($imagick->getImageBlob());
        }

        $imagick->clear();
        $imagick->destroy();

        return $result;
    }

    /**
     * Render QR code matrix to SVG
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
            // Handle both string and array formats
            $lineData = is_string($line) ? str_split($line) : $line;
            foreach ($lineData as $col => $module) {
                if ((is_string($module) && $module === '1') || (is_numeric($module) && ($module & 1))) {
                    $x = $margin + $col * $moduleSize;
                    $y = $margin + $row * $moduleSize;
                    $rects[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$moduleSize}\" height=\"{$moduleSize}\" fill=\"{$fgColor}\"/>";
                }
            }
        }

        $svg .= implode("\n", $rects)."\n</svg>";

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
