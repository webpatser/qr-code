<?php

namespace QR_Code\Util;

/**
 * Array optimization utilities using PHP 8.2+ features
 * 
 * @package QR_Code\Util
 */
class ArrayOptimizer
{
    /**
     * Optimize QR matrix data using PHP 8.2 array functions
     */
    public static function optimizeMatrix(array $matrix): array
    {
        // Check if all rows are strings for optimization
        $allStrings = true;
        $hasArrays = false;
        
        foreach ($matrix as $row) {
            if (!is_string($row)) {
                $allStrings = false;
                $hasArrays = true;
                break;
            }
        }

        if ($allStrings) {
            // All rows are strings, optimize by avoiding string conversion
            return $matrix;
        }

        if ($hasArrays) {
            // Mixed types detected, normalize
            return array_map(fn($row) => is_string($row) ? $row : implode('', $row), $matrix);
        }

        return $matrix;
    }

    /**
     * Fast module counting using PHP 8.2 optimizations
     */
    public static function countBlackModules(array $matrix): int
    {
        $count = 0;
        
        foreach ($matrix as $row) {
            if (is_string($row)) {
                // Use substr_count for string rows (PHP 8.4 optimized)
                $count += substr_count($row, '1');
            } else {
                // Use array_count_values for array rows
                $values = array_count_values($row);
                $count += $values[1] ?? 0;
            }
        }
        
        return $count;
    }

    /**
     * Batch process QR matrices efficiently
     */
    public static function batchProcess(array $matrices, callable $processor): array
    {
        // Use array_map with unpacking for better performance in PHP 8.4
        return array_map($processor, ...[array_values($matrices)]);
    }

    /**
     * Optimized color palette generation
     */
    public static function generateColorPalette(int $backgroundColor, int $foregroundColor): array
    {
        $bg = [
            'r' => ($backgroundColor >> 16) & 0xFF,
            'g' => ($backgroundColor >> 8) & 0xFF,
            'b' => $backgroundColor & 0xFF,
        ];
        
        $fg = [
            'r' => ($foregroundColor >> 16) & 0xFF,
            'g' => ($foregroundColor >> 8) & 0xFF,
            'b' => $foregroundColor & 0xFF,
        ];

        // Use array_combine with range for efficient palette generation
        $steps = range(0, 255, 51); // 6 levels for web-safe colors
        
        return [
            'background' => $bg,
            'foreground' => $fg,
            'palette' => array_map(
                fn($r, $g, $b) => ($r << 16) | ($g << 8) | $b,
                array_fill(0, count($steps), $bg['r']),
                array_fill(0, count($steps), $bg['g']),
                $steps
            ),
        ];
    }

    /**
     * Memory-efficient matrix compression
     */
    public static function compressMatrix(array $matrix): string
    {
        // Convert matrix to binary string for better memory efficiency
        $binary = '';
        foreach ($matrix as $row) {
            if (is_string($row)) {
                $binary .= $row;
            } else {
                $binary .= implode('', $row);
            }
        }
        
        // Use gzcompress with level 6 for good compression/speed balance
        return gzcompress($binary, 6);
    }

    /**
     * Decompress matrix data
     */
    public static function decompressMatrix(string $compressed, int $size): array
    {
        $binary = gzuncompress($compressed);
        $matrix = [];
        
        for ($i = 0; $i < $size; $i++) {
            $matrix[] = substr($binary, $i * $size, $size);
        }
        
        return $matrix;
    }

    /**
     * Fast matrix validation using PHP 8.4 features
     */
    public static function validateMatrix(array $matrix): bool
    {
        // Check if matrix is square
        $size = count($matrix);
        
        // Validate each row for PHP 8.2 compatibility
        foreach ($matrix as $row) {
            if (is_string($row) && strlen($row) !== $size) {
                return false;
            }
            if (is_array($row) && count($row) !== $size) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Optimized matrix rotation using array operations
     */
    public static function rotateMatrix(array $matrix, int $degrees = 90): array
    {
        if ($degrees % 90 !== 0) {
            throw new \InvalidArgumentException('Rotation must be in 90-degree increments');
        }
        
        $rotations = ($degrees / 90) % 4;
        
        for ($i = 0; $i < $rotations; $i++) {
            $matrix = self::rotateMatrix90($matrix);
        }
        
        return $matrix;
    }

    /**
     * Single 90-degree rotation optimized for PHP 8.2
     */
    private static function rotateMatrix90(array $matrix): array
    {
        $size = count($matrix);
        $rotated = array_fill(0, $size, array_fill(0, $size, '0'));
        
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $value = is_string($matrix[$i]) ? $matrix[$i][$j] : $matrix[$i][$j];
                $rotated[$j][$size - 1 - $i] = $value;
            }
        }
        
        // Convert back to string format if original was strings
        if (is_string($matrix[0])) {
            return array_map('implode', $rotated);
        }
        
        return $rotated;
    }
}