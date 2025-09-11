<?php

namespace QR_Code\Types;

use QR_Code\Contracts\CodeType;
use QR_Code\Util\AbstractGenerator;

/**
 * Class QR_Url
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 */
class QR_Url extends AbstractGenerator implements CodeType
{
    protected $url;

    /**
     * URL QR Code
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get Formatted QR Code String
     */
    public function getCodeString(): string
    {
        return preg_match("#^https?\:\/\/#", $this->url) ? $this->url : "http://{$this->url}";
    }
}
