<?php

namespace QR_Code\Encoder;

/**
 * QR Code Image class
 *
 * Based on PHP QR Code distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * QR Code Generator for PHP is distributed under MIT
 * Copyright (C) 2018 Bruno Vaula Werneck <brunovaulawerneck at gmail dot com>
 *
 * @package QR_Code\Encoder
 */
class Image
{
    /**
     * @param array $frame
     * @param string|bool  $filename
     * @param int   $pixelPerPoint
     * @param int   $outerFrame
     * @param bool  $saveAndPrint
     * @param int   $backColor
     * @param int   $foreColor
     */
    public static function png (array $frame, $filename = false, int $pixelPerPoint = 4, int $outerFrame = 4, bool $saveAndPrint = false, int $backColor = QR_WHITE, int $foreColor = QR_BLACK) : string
    {
        $image = self::image($frame, $pixelPerPoint, $outerFrame, $backColor, $foreColor);


        if ($filename === false) {
            ob_start();
            ImagePng($image);
            $qr = ob_get_clean();
        } else {
            if ($saveAndPrint === true) {
                ImagePng($image, $filename);
                ImagePng($image);
            } else {
                ob_start();
                ImagePng($image);
                $qr = ob_get_clean();
            }
        }

        ImageDestroy($image);

        return ('data:image/png;base64, ' . base64_encode($qr));

    }

    /**
     * Stream JPEG Image
     *
     * @param array       $frame
     * @param string|bool $filename
     * @param int         $pixelPerPoint
     * @param int         $outerFrame
     * @param int         $quality
     */
    public static function jpg (array $frame, $filename = false, int $pixelPerPoint = 8, int $outerFrame = 4, int $quality = 85) : void
    {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);

        if ($filename === false) {
            ImageJpeg($image, null, $quality);
        } else {
            ImageJpeg($image, $filename, $quality);
        }

        ImageDestroy($image);
    }

    /**
     * Get Image Resource
     *
     * @param array $frame
     * @param int   $pixelPerPoint
     * @param int   $outerFrame
     * @param int   $backColor
     * @param int   $foreColor
     * @return resource
     */
    public static function image (array $frame, int $pixelPerPoint = 4, int $outerFrame = 4, int $backColor = QR_WHITE, int $foreColor = QR_BLACK)
    {
        $h = count($frame);
        $w = strlen($frame[0]);

        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;

        $base_image = ImageCreate($imgW, $imgH);

        /**
         * convert a hexadecimal color code into decimal format (red = 255 0 0, green = 0 255 0, blue = 0 0 255)
         */
        $r1 = round((($foreColor & 0xFF0000) >> 16), 5);
        $g1 = round((($foreColor & 0x00FF00) >> 8), 5);
        $b1 = round(($foreColor & 0x0000FF), 5);

        /**
         * convert a hexadecimal color code into decimal format (red = 255 0 0, green = 0 255 0, blue = 0 0 255)
         */
        $r2 = round((($backColor & 0xFF0000) >> 16), 5);
        $g2 = round((($backColor & 0x00FF00) >> 8), 5);
        $b2 = round(($backColor & 0x0000FF), 5);


        $col[0] = ImageColorAllocate($base_image, $r2, $g2, $b2);
        $col[1] = ImageColorAllocate($base_image, $r1, $g1, $b1);

        imagefill($base_image, 0, 0, $col[0]);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    ImageSetPixel($base_image, $x + $outerFrame, $y + $outerFrame, $col[1]);
                }
            }
        }

        $target_image = ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
        ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
        ImageDestroy($base_image);

        return $target_image;
    }
}
