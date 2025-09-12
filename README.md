# QR Code Generator

[![Total Downloads](https://img.shields.io/packagist/dt/webpatser/qr-code.svg)](https://packagist.org/packages/webpatser/qr-code)
[![PHP Version](https://img.shields.io/packagist/php-v/webpatser/qr-code.svg)](https://packagist.org/packages/webpatser/qr-code)
[![License](https://img.shields.io/packagist/l/webpatser/qr-code.svg)](https://packagist.org/packages/webpatser/qr-code)

Modern PHP QR Code library with 3300x performance boost, multiple formats, and 11 QR code types. Restored from deleted original project.

## Installation

```bash
composer require webpatser/qr-code
```

**Requirements:** PHP 8.2+ (GD extension optional for PNG/JPEG generation)

## Quick Start

```php
use QR_Code\QR_Code;

// Generate a simple QR code as PNG
$result = QR_Code::png('Hello World!', 'qrcode.png');

// Generate SVG
QR_Code::svg('Hello World!', 'qrcode.svg');

// Get as base64 data URL (no file)
$dataUrl = QR_Code::png('Hello World!');
echo $dataUrl; // data:image/png;base64,...
```

## Documentation

For complete documentation, examples, and API reference, visit:

**https://documentation.downsized.nl/qr-code**

## License

MIT License.