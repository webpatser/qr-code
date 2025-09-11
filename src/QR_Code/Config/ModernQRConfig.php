<?php

namespace QR_Code\Config;

use QR_Code\Enums\ErrorCorrectionLevel;

/**
 * Modern QR Configuration for PHP 8.2+
 */
class ModernQRConfig
{
    private ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low;

    private int $size = 3;

    private int $margin = 4;

    private int $backgroundColor;

    private int $foregroundColor;

    private bool $saveAndPrint = false;

    private int $version = 0;

    public function __construct()
    {
        $this->backgroundColor = QR_WHITE;
        $this->foregroundColor = QR_BLACK;
    }

    /**
     * Get error correction level
     */
    public function getErrorCorrectionLevel(): ErrorCorrectionLevel
    {
        return $this->errorCorrectionLevel;
    }

    /**
     * Set error correction level with validation
     */
    public function setErrorCorrectionLevel(ErrorCorrectionLevel $errorCorrectionLevel): self
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }

    /**
     * Get size
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set size with validation
     */
    public function setSize(int $size): self
    {
        if ($size < 1 || $size > 50) {
            throw new \InvalidArgumentException('Size must be between 1 and 50');
        }
        $this->size = $size;

        return $this;
    }

    /**
     * Get margin
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * Set margin with validation
     */
    public function setMargin(int $margin): self
    {
        if ($margin < 0 || $margin > 20) {
            throw new \InvalidArgumentException('Margin must be between 0 and 20');
        }
        $this->margin = $margin;

        return $this;
    }

    /**
     * Get background color
     */
    public function getBackgroundColor(): int
    {
        return $this->backgroundColor;
    }

    /**
     * Set background color with validation
     */
    public function setBackgroundColor(int $backgroundColor): self
    {
        if ($backgroundColor < 0 || $backgroundColor > 0xFFFFFF) {
            throw new \InvalidArgumentException('Invalid background color value');
        }
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Get foreground color
     */
    public function getForegroundColor(): int
    {
        return $this->foregroundColor;
    }

    /**
     * Set foreground color with validation
     */
    public function setForegroundColor(int $foregroundColor): self
    {
        if ($foregroundColor < 0 || $foregroundColor > 0xFFFFFF) {
            throw new \InvalidArgumentException('Invalid foreground color value');
        }
        $this->foregroundColor = $foregroundColor;

        return $this;
    }

    /**
     * Get save and print flag
     */
    public function getSaveAndPrint(): bool
    {
        return $this->saveAndPrint;
    }

    /**
     * Set save and print flag
     */
    public function setSaveAndPrint(bool $saveAndPrint): self
    {
        $this->saveAndPrint = $saveAndPrint;

        return $this;
    }

    /**
     * Get version
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set version with validation
     */
    public function setVersion(int $version): self
    {
        if ($version < 0 || $version > 40) {
            throw new \InvalidArgumentException('Version must be between 0 and 40');
        }
        $this->version = $version;

        return $this;
    }

    // Legacy property access for backward compatibility
    public function __get(string $property): mixed
    {
        return match ($property) {
            'errorCorrectionLevel' => $this->errorCorrectionLevel,
            'size' => $this->size,
            'margin' => $this->margin,
            'backgroundColor' => $this->backgroundColor,
            'foregroundColor' => $this->foregroundColor,
            'saveAndPrint' => $this->saveAndPrint,
            'version' => $this->version,
            default => throw new \InvalidArgumentException("Property $property does not exist")
        };
    }

    public function __set(string $property, mixed $value): void
    {
        match ($property) {
            'errorCorrectionLevel' => $this->setErrorCorrectionLevel($value),
            'size' => $this->setSize($value),
            'margin' => $this->setMargin($value),
            'backgroundColor' => $this->setBackgroundColor($value),
            'foregroundColor' => $this->setForegroundColor($value),
            'saveAndPrint' => $this->setSaveAndPrint($value),
            'version' => $this->setVersion($value),
            default => throw new \InvalidArgumentException("Property $property does not exist")
        };
    }

    /**
     * Create a new configuration with modified properties (immutable pattern)
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
        $new = clone $this;
        if ($errorCorrectionLevel !== null) {
            $new->setErrorCorrectionLevel($errorCorrectionLevel);
        }
        if ($size !== null) {
            $new->setSize($size);
        }
        if ($margin !== null) {
            $new->setMargin($margin);
        }
        if ($backgroundColor !== null) {
            $new->setBackgroundColor($backgroundColor);
        }
        if ($foregroundColor !== null) {
            $new->setForegroundColor($foregroundColor);
        }
        if ($saveAndPrint !== null) {
            $new->setSaveAndPrint($saveAndPrint);
        }
        if ($version !== null) {
            $new->setVersion($version);
        }

        return $new;
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

    /**
     * Factory methods with validation
     */
    public static function default(): self
    {
        return new self;
    }

    public static function highQuality(): self
    {
        $config = new self;

        return $config->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(5)
            ->setMargin(4);
    }

    public static function compact(): self
    {
        $config = new self;

        return $config->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(2)
            ->setMargin(1);
    }

    public static function create(
        ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::Low,
        int $size = 3,
        int $margin = 4,
        int $backgroundColor = QR_WHITE,
        int $foregroundColor = QR_BLACK,
        bool $saveAndPrint = false,
        int $version = 0,
    ): self {
        $config = new self;

        return $config->setErrorCorrectionLevel($errorCorrectionLevel)
            ->setSize($size)
            ->setMargin($margin)
            ->setBackgroundColor($backgroundColor)
            ->setForegroundColor($foregroundColor)
            ->setSaveAndPrint($saveAndPrint)
            ->setVersion($version);
    }
}
