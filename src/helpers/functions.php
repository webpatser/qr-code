<?php

if (! function_exists('isPng')) {
    /**
     * Checks if file is a PNG image
     */
    function isPng(string $filename): bool
    {
        return exif_imagetype($filename) === IMAGETYPE_PNG;
    }
}

if (! function_exists('isSvg')) {
    /**
     * Checks if file is an SVG image
     */
    function isSvg(string $filename): bool
    {
        return mime_content_type($filename) === 'image/svg+xml';
    }
}

if (! function_exists('inString')) {
    /**
     * Checks if substring exists in string
     */
    function inString(string $haystack, string $needle): bool
    {
        if (strpos($haystack, $needle) !== false) {
            return true;
        }

        return false;
    }
}

if (! function_exists('validateErrorCorrectionLevel')) {
    /**
     * Validates Error Correction Level
     */
    function validateErrorCorrectionLevel(string $errorCorrectionLevel): bool
    {
        $allowedValues = ['L', 'M', 'Q', 'H'];

        /**
         * Must be 1 char long
         */
        if (strlen($errorCorrectionLevel) > 1) {
            return false;
        }
        /**
         * Must be in $allowedValues array
         */
        if (! in_array($errorCorrectionLevel, $allowedValues)) {
            return false;
        }

        return true;
    }
}

if (! function_exists('dropFilesBySubstring')) {
    /**
     * Deletes file(s) from a directory
     */
    function dropFilesBySubstring(string $directory, string $substring): void
    {
        $files = scandir($directory);

        foreach ($files as $file) {
            if (inString($file, $substring)) {
                unlink($directory.$file);
            }
        }
    }
}

if (! function_exists('startsWith')) {
    /**
     * Checks if string starts with substring
     */
    function startsWith(string $needle, string $haystack): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

if (! function_exists('strSet')) {
    /**
     * I have absolutely no idea what this does
     *
     * @param  bool|int  $replLen
     */
    function strSet(array &$srctab, int $x, int $y, string $repl, $replLen = false): void
    {
        $srctab[$y] = substr_replace($srctab[$y], ($replLen !== false) ? substr($repl, 0, $replLen) : $repl, $x, ($replLen !== false) ? $replLen : strlen($repl));
    }
}
