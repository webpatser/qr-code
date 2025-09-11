<?php

namespace QR_Code\Config;

use QR_Code\Enums\ErrorCorrectionLevel;

/**
 * Modern QR Code Configuration using PHP 8.2+ features
 */
readonly class QRConfig
{
    public function __construct(
        public ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low,
        public int $size = 3,
        public int $margin = 4,
        public int $backgroundColor = QR_WHITE,
        public int $foregroundColor = QR_BLACK,
        public bool $saveAndPrint = false,
        public int $version = 0,
    ) {
        if ($this->size < 1 || $this->size > 50) {
            throw new \InvalidArgumentException('Size must be between 1 and 50');
        }

        if ($this->margin < 0 || $this->margin > 20) {
            throw new \InvalidArgumentException('Margin must be between 0 and 20');
        }

        if ($this->version < 0 || $this->version > 40) {
            throw new \InvalidArgumentException('Version must be between 0 and 40');
        }
    }

    /**
     * Create a configuration with modified properties
     */
    public function with(
        ?ErrorCorrectionLevel $errorCorrectionLevel = null,
        ?int $size = null,
        ?int $margin = null,
        ?int $backgroundColor = null,
        ?int $foregroundColor = null,
        ?bool $saveAndPrint = null,
        ?int $version = null,
    ): self {
        return new self(
            $errorCorrectionLevel ?? $this->errorCorrectionLevel,
            $size ?? $this->size,
            $margin ?? $this->margin,
            $backgroundColor ?? $this->backgroundColor,
            $foregroundColor ?? $this->foregroundColor,
            $saveAndPrint ?? $this->saveAndPrint,
            $version ?? $this->version,
        );
    }

    /**
     * Create default configuration
     */
    public static function default(): self
    {
        return new self;
    }

    /**
     * Create high-quality configuration
     */
    public static function highQuality(): self
    {
        return new self(
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 5,
            margin: 4,
        );
    }

    /**
     * Create compact configuration
     */
    public static function compact(): self
    {
        return new self(
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 2,
            margin: 1,
        );
    }

    /**
     * Get legacy error correction level value for backward compatibility
     */
    public function getLegacyErrorCorrectionLevel(): int
    {
        return $this->errorCorrectionLevel->toLegacyConstant();
    }

    /**
     * Get error correction level as string
     */
    public function getErrorCorrectionLevelString(): string
    {
        return $this->errorCorrectionLevel->toString();
    }
}
