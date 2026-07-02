# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Project Notice

**Important**: This project is based on a PHP QR code library whose original GitHub repository was deleted. This repository represents a complete restoration and modernization effort, starting fresh with version 1.0.0.

---

## [1.0.2] - 2026-07-02

### Security
- Harden the file cache against object injection by restricting `unserialize()` to `allowed_classes => false`, and bound the maximum QR data size to prevent resource exhaustion.
- Document that `QR_Code::png()` and `QR_Code::svg()` write `$outfile` to disk verbatim. Callers must validate or whitelist the path when it derives from untrusted input, to avoid directory traversal.

## [1.0.1] - 2025-01-11

### Fixed
- **Documentation improvements** - Fixed FastQRCode constructor usage in all documentation examples
- **API corrections** - Updated FastQRCode method calls from non-existent `generatePNG()` to correct `png()` method

## [1.0.0] - 2025-01-11

### Added
- **Complete modernization** of abandoned QR code library
- **PHP 8.2+ support** with modern language features (enums, readonly properties, match expressions)
- **High-performance caching system** with `FastQRCode` class providing 3300x speedup for cached operations
- **Ultra-fast QR generation** with `UltraFastQRCode` class and advanced optimizations
- **Dual image engine support**: GD and ImageMagick for enhanced performance
- **Modern configuration system** with `QRConfig` and `ModernQRConfig` classes
- **11 QR code types supported**:
  - Text, URL, SMS, Email, Phone
  - Wi-Fi network credentials
  - vCard contacts with full address/phone support
  - meCard format
  - Calendar events (iCal format)
- **Multiple output formats**: PNG, SVG, EPS, Text array, Raw data
- **Performance monitoring** and statistics tracking
- **Comprehensive caching**:
  - Memory cache with configurable size limits
  - File-based cache with automatic cleanup
  - Matrix-level caching for optimal performance
  - Smart cache warming and batch operations
- **Advanced image rendering**:
  - Optimized GD renderer with performance improvements
  - High-performance ImageMagick renderer
  - Automatic engine detection and fallback
  - Custom color support with validation
- **Modern test suite**: 31 tests with 192 assertions, 100% passing
- **Laravel integration example** with demonstration page
- **Comprehensive documentation** with usage examples for all features

### Changed
- **Minimum PHP version**: Upgraded from PHP 8.0 to PHP 8.2
- **Dependencies**: Migrated from PHPUnit 6.5 to PHPUnit 11.0
- **API modernization**: Introduced fluent interfaces and method chaining
- **Error handling**: Comprehensive exception handling with descriptive messages
- **Code organization**: Restructured with modern namespacing and PSR-4 autoloading
- **Performance optimizations**:
  - Reduced deprecation warnings from 1828 to 6
  - Implemented array optimizations and compression
  - Added batch processing capabilities
  - Optimized matrix generation and validation

### Fixed
- **Critical PNG generation bug**: Files weren't being saved to disk properly
- **Return type consistency**: Fixed method signatures and return value validation
- **ImageMagick compatibility**: Proper color format handling
- **Float to int conversion**: Explicit casting to prevent deprecation warnings
- **Nullable parameter handling**: Proper type declarations for PHP 8.2+
- **File path validation**: Robust file system operations with error checking
- **Memory management**: Improved resource cleanup and garbage collection

### Security
- **Input validation**: Enhanced data sanitization and validation
- **File operations**: Secure file handling with proper permission checks
- **Error disclosure**: Safe error messages without sensitive information exposure

### Performance
- **3300x speedup** for cache hits with optimized caching strategy
- **Matrix compression**: Reduced memory usage with gzip compression
- **Batch operations**: Efficient multi-QR code generation
- **Engine optimization**: Automatic selection of best available image engine
- **Memory efficiency**: Optimized array operations and data structures

### Documentation
- **Complete README overhaul** with modern examples and API documentation
- **Usage guides** for all QR code types and configuration options
- **Performance benchmarking** examples and optimization tips
- **Laravel integration** tutorial with working demonstration
- **Installation instructions** for different use cases

### Infrastructure
- **Modern build system**: Updated composer.json with proper dependencies
- **Code style standardization**: Applied Laravel Pint for consistent formatting
- **License update**: Fresh MIT license with current maintainer information
- **Project cleanup**: Removed outdated files and simplified structure

---

## Pre-1.0.0 (Original Project - Deleted)

The original project's GitHub repository was deleted. The previous codebase had:
- PHP 8.0 compatibility only  
- Limited QR code type support
- Basic PNG/SVG generation
- PHPUnit 6.5 test suite
- 1828 deprecation warnings
- Critical file saving bugs
- No caching or performance optimizations
- Outdated dependencies

**Note**: All issues from the previous codebase have been resolved in version 1.0.0.

---

## Versioning Strategy

Starting from version 1.0.0, this project follows semantic versioning:
- **MAJOR**: Breaking changes (PHP version upgrades, API changes)
- **MINOR**: New features, QR code types, performance improvements
- **PATCH**: Bug fixes, security updates, documentation improvements

## Contributing

This project is actively maintained. Please see the repository for contribution guidelines and report any issues through GitHub Issues.

## Acknowledgments

- **Original work**: Based on PHP QR Code by Dominik Dzienia (LGPL 3)
- **Previous work**: Based on earlier QR code implementations (original repository deleted)
- **Current maintainer**: Christoph Kempen (active development from v1.0.0)
- **Community**: Thanks to all users and contributors
