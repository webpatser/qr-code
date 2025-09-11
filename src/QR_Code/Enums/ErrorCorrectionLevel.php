<?php

namespace QR_Code\Enums;

/**
 * QR Code Error Correction Levels
 * 
 * @package QR_Code\Enums
 */
enum ErrorCorrectionLevel: int
{
    case Low = 0;      // ~7% error correction (QR_ECLEVEL_L)
    case Medium = 1;   // ~15% error correction (QR_ECLEVEL_M)
    case Quartile = 2; // ~25% error correction (QR_ECLEVEL_Q)
    case High = 3;     // ~30% error correction (QR_ECLEVEL_H)

    /**
     * Get the string representation of the error correction level
     */
    public function toString(): string
    {
        return match ($this) {
            self::Low => 'L',
            self::Medium => 'M',
            self::Quartile => 'Q',
            self::High => 'H',
        };
    }

    /**
     * Create from string representation
     */
    public static function fromString(string $level): self
    {
        return match (strtoupper($level)) {
            'L' => self::Low,
            'M' => self::Medium,
            'Q' => self::Quartile,
            'H' => self::High,
            default => throw new \InvalidArgumentException("Invalid error correction level: $level"),
        };
    }

    /**
     * Get legacy constant value for backward compatibility
     */
    public function toLegacyConstant(): int
    {
        return $this->value;
    }
}