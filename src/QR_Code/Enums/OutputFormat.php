<?php

namespace QR_Code\Enums;

/**
 * QR Code Output Formats
 */
enum OutputFormat: int
{
    case Text = 0;  // QR_FORMAT_TEXT
    case PNG = 1;   // QR_FORMAT_PNG

    /**
     * Get the string representation of the output format
     */
    public function toString(): string
    {
        return match ($this) {
            self::Text => 'TEXT',
            self::PNG => 'PNG',
        };
    }

    /**
     * Get file extension for the format
     */
    public function getExtension(): string
    {
        return match ($this) {
            self::Text => 'txt',
            self::PNG => 'png',
        };
    }

    /**
     * Get MIME type for the format
     */
    public function getMimeType(): string
    {
        return match ($this) {
            self::Text => 'text/plain',
            self::PNG => 'image/png',
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
