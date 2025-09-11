# PHP QR Code Generator

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE.md)
[![Tests](https://img.shields.io/badge/tests-31%20passed-brightgreen)](tests/)

A modern, robust QR Code generator for PHP with support for multiple output formats and QR code types. Generate QR codes for text, URLs, contacts, Wi-Fi credentials, calendar events, and more.

## ✨ Features

- 🖼️ **Multiple Output Formats**: PNG, SVG, EPS, Text, Raw
- 📱 **Rich QR Code Types**: Text, URL, SMS, Email, Phone, Wi-Fi, vCard, meCard, Calendar Events
- 🎨 **Customizable**: Size, margin, colors, error correction levels
- 🚀 **Modern PHP**: Built for PHP 8.2+ with enums, readonly properties, and advanced optimizations
- ⚡ **High Performance**: Advanced caching, ImageMagick support, batch operations
- 🧪 **Well Tested**: 31 tests with comprehensive coverage
- 📦 **Easy Integration**: PSR-4 autoloading, Composer ready

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- **Optional**: GD extension (for PNG/JPEG generation)
- **Optional**: ImageMagick extension (for enhanced performance and additional formats)

## 🚀 Installation

Install via Composer:

```bash
composer require webpatser/qr-code
```

## 📖 Quick Start

### Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use QR_Code\QR_Code;

// Generate a simple QR code as PNG
$result = QR_Code::png('Hello World!', 'qrcode.png');

// Generate SVG
QR_Code::svg('Hello World!', 'qrcode.svg');

// Get as base64 data URL (no file)
$dataUrl = QR_Code::png('Hello World!');
echo $dataUrl; // data:image/png;base64,...
```

### Advanced Options

```php
// Custom size, margin, colors, and error correction
QR_Code::png(
    'Custom QR Code',
    'custom.png',
    'H',          // Error correction level (L, M, Q, H)
    10,           // Size multiplier
    2,            // Margin
    false,        // Save and print
    QR_WHITE,     // Background color
    QR_BLACK      // Foreground color
);
```

## 🔧 QR Code Types

### Text
```php
use QR_Code\Types\QR_Text;

$qr = new QR_Text('Plain text message');
$qr->setOutfile('text.png')->png();
```

### URL
```php
use QR_Code\Types\QR_Url;

$qr = new QR_Url('https://github.com/christoph/qr-code');
$qr->setOutfile('url.png')->png();
```

### Phone
```php
use QR_Code\Types\QR_Phone;

$qr = new QR_Phone('+1-555-123-4567');
$qr->setOutfile('phone.png')->png();
```

### SMS
```php
use QR_Code\Types\QR_Sms;

$qr = new QR_Sms('+1-555-123-4567', 'Hello from QR Code!');
$qr->setOutfile('sms.png')->png();
```

### Email
```php
use QR_Code\Types\QR_EmailMessage;

$qr = new QR_EmailMessage(
    'contact@example.com',
    'Subject Line',
    'Email body content'
);
$qr->setOutfile('email.png')->png();
```

### Wi-Fi Network
```php
use QR_Code\Types\QR_WiFi;

$qr = new QR_WiFi(
    'WPA2',           // Security type
    'MyNetwork',      // SSID
    'mypassword',     // Password
    false             // Hidden network
);
$qr->setOutfile('wifi.png')->png();
```

### vCard Contact
```php
use QR_Code\Types\QR_VCard;
use QR_Code\Types\vCard\Person;
use QR_Code\Types\vCard\Phone;
use QR_Code\Types\vCard\Address;

$person = new Person('John', 'Doe', 'Mr.', 'john.doe@example.com');
$phone = new Phone('HOME', '+1-555-123-4567');
$address = new Address('HOME', true, '123 Main St', 'City', 'ST', '12345', 'USA');

$vcard = new QR_VCard($person, [$phone], [$address]);
$vcard->setOutfile('vcard.png')->png();
```

### Calendar Event
```php
use QR_Code\Types\QR_CalendarEvent;

$startTime = new DateTime('2024-12-25 18:00:00');
$endTime = new DateTime('2024-12-25 22:00:00');

$event = new QR_CalendarEvent($startTime, $endTime, 'Christmas Dinner');
$event->setOutfile('event.png')->png();
```

## ⚡ High-Performance API

For maximum performance, use the optimized `FastQRCode` class:

### Basic Usage
```php
use QR_Code\FastQRCode;
use QR_Code\Config\QRConfig;
use QR_Code\Enums\ErrorCorrectionLevel;

// Create high-performance instance
$fastQR = new FastQRCode();

// Generate with automatic caching
$dataUrl = $fastQR->png('High-speed QR generation');

// Custom configuration
$config = QRConfig::highQuality()->with(
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 6
);
$result = $fastQR->png('Custom QR Code', $config);
```

### Batch Operations
```php
// Generate multiple QR codes efficiently
$data = ['URL 1', 'URL 2', 'URL 3'];
$results = $fastQR->batch($data, $config);
```

### Engine Selection
```php
use QR_Code\Enums\ImageEngine;

// Check available engines
$engines = $fastQR->getAvailableEngines();

// Use specific engine for best performance
$fastQR->setImageEngine(ImageEngine::Imagick); // Fastest
$fastQR->setImageEngine(ImageEngine::GD);      // Good compatibility
```

### Caching Performance
```php
use QR_Code\Cache\MemoryCache;
use QR_Code\Cache\FileCache;

// Ultra-fast memory cache
$fastQR = new FastQRCode(new MemoryCache());

// Persistent file cache
$fastQR = new FastQRCode(new FileCache('/tmp/qr_cache'));

// Get cache statistics
$stats = $fastQR->getCacheStats();
```

## 🎨 Output Formats

### PNG Image
```php
// Save to file
QR_Code::png('Content', 'output.png');

// Get as data URL
$dataUrl = QR_Code::png('Content');
```

### SVG Vector
```php
QR_Code::svg('Content', 'output.svg');
```

### Text Array
```php
$textArray = QR_Code::text('Content');
// Returns array of strings representing QR code matrix
```

### Raw Data
```php
$rawData = QR_Code::raw('Content', 'output.txt');
// Returns raw QR code data array
```

## ⚙️ Configuration Options

### Error Correction Levels
- **L** (Low): ~7% error correction
- **M** (Medium): ~15% error correction
- **Q** (Quartile): ~25% error correction
- **H** (High): ~30% error correction

### Size and Appearance
```php
QR_Code::png(
    'Content',
    'styled.png',
    'M',              // Error correction
    8,                // Size (pixel multiplier)
    3,                // Margin (in modules)
    false,            // Save and print flag
    0xFFFFFF,         // Background color (white)
    0x000000          // Foreground color (black)
);
```

### Color Constants
```php
QR_WHITE  // 0xFFFFFF
QR_BLACK  // 0x000000
QR_RED    // 0xFF0000
QR_GREEN  // 0x00FF00
QR_BLUE   // 0x0000FF
```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage
```

## 📁 Project Structure

```
src/
├── QR_Code/
│   ├── QR_Code.php           # Main QR code class
│   ├── Types/                # QR code type implementations
│   ├── Encoder/              # Core encoding logic
│   ├── Util/                 # Utility classes
│   └── Config/               # Configuration classes
├── helpers/
│   ├── constants.php         # Global constants
│   └── functions.php         # Helper functions
tests/                        # Test suite
public/                       # Demo application
docs/                         # Documentation
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Follow PSR coding standards

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 🙏 Acknowledgments

- Based on the original PHP QR Code library by Dominik Dzienia
- Uses algorithms and specifications from the QR Code standard
- Special thanks to all contributors and maintainers

## 📚 Additional Resources

- [QR Code specification](https://www.iso.org/standard/62021.html)
- [Error correction levels explained](https://en.wikipedia.org/wiki/QR_code#Error_correction)
- [vCard format specification](https://tools.ietf.org/html/rfc6350)

---

**Made with ❤️ for the PHP community**