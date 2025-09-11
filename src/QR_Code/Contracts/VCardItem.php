<?php

namespace QR_Code\Contracts;

/**
 * Interface VCardItem
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 */
interface VCardItem
{
    /**
     * Gets vCard Item Text
     */
    public function __toString(): string;
}
