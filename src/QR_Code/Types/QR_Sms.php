<?php

namespace QR_Code\Types;

use QR_Code\Contracts\CodeType;
use QR_Code\Util\AbstractGenerator;

/**
 * Class QR_Sms
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 */
class QR_Sms extends AbstractGenerator implements CodeType
{
    protected $phone;

    protected $text;

    /**
     * SMS QR Code
     */
    public function __construct(string $phone, string $text)
    {
        $this->phone = $phone;
        $this->text = $text;
    }

    /**
     * Get Formatted QR Code String
     */
    public function getCodeString(): string
    {
        return "SMSTO:{$this->phone}:{$this->text}";
    }
}
