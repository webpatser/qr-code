<?php

namespace QR_Code\Types;

use QR_Code\Contracts\CodeType;
use QR_Code\Util\AbstractGenerator;

/**
 * Class QR_Text
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 */
class QR_Text extends AbstractGenerator implements CodeType
{
    public function __construct(
        private readonly string $data
    ) {
        if (empty($this->data)) {
            throw new \InvalidArgumentException('Text data cannot be empty');
        }
    }

    /**
     * Get Formatted QR Code String
     */
    public function getCodeString(): string
    {
        return $this->data;
    }
}
