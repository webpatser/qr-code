<?php

namespace QR_Code\Enums;

/**
 * QR Code Encoding Modes
 * 
 * @package QR_Code\Enums
 */
enum EncodingMode: int
{
    case Null = -1;       // QR_MODE_NUL
    case Numeric = 0;     // QR_MODE_NUM
    case Alphanumeric = 1; // QR_MODE_AN
    case Byte = 2;        // QR_MODE_8
    case Kanji = 3;       // QR_MODE_KANJI
    case Structure = 4;   // QR_MODE_STRUCTURE

    /**
     * Get the string representation of the encoding mode
     */
    public function toString(): string
    {
        return match ($this) {
            self::Null => 'NULL',
            self::Numeric => 'NUMERIC',
            self::Alphanumeric => 'ALPHANUMERIC',
            self::Byte => 'BYTE',
            self::Kanji => 'KANJI',
            self::Structure => 'STRUCTURE',
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