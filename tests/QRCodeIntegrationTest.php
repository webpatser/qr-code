<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Zxing\QrReader;

class QRCodeIntegrationTest extends TestCase
{
    protected $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = __DIR__.DIRECTORY_SEPARATOR.'integration_temp'.DIRECTORY_SEPARATOR;
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up temp directory
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir.'*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->tempDir);
        }
    }

    public function test_complete_qr_code_workflow()
    {
        $testData = [
            'simple_text' => 'Hello World!',
            'url' => 'https://github.com/werneckbh/qr-code',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'unicode' => 'Unicode: 中文 日本語 한글 🎉',
            'numbers' => '1234567890',
            'special' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
        ];

        foreach ($testData as $testName => $data) {
            $filename = $this->tempDir."test_{$testName}.png";

            // Generate QR code
            $result = \QR_Code\QR_Code::png($data, $filename);

            // Verify file creation
            $this->assertTrue(file_exists($filename), "QR code file not created for {$testName}");
            $this->assertTrue(isPng($filename), "File is not PNG for {$testName}");

            // Verify file size is reasonable
            $this->assertGreaterThan(100, filesize($filename), "File too small for {$testName}");
            $this->assertLessThan(10000, filesize($filename), "File too large for {$testName}");

            // Decode and verify content
            $reader = new QrReader($filename);
            $decodedText = $reader->text();

            $this->assertEquals($data, $decodedText, "Decoded text doesn't match original for {$testName}");
        }
    }

    public function test_qr_code_type_classes()
    {
        $types = [
            'text' => new \QR_Code\Types\QR_Text('Test Text'),
            'url' => new \QR_Code\Types\QR_Url('https://example.com'),
            'phone' => new \QR_Code\Types\QR_Phone('+1-555-123-4567'),
            'sms' => new \QR_Code\Types\QR_Sms('+1-555-123-4567', 'Hello SMS'),
            'email' => new \QR_Code\Types\QR_EmailMessage('test@example.com', 'Subject', 'Body'),
            'wifi' => new \QR_Code\Types\QR_WiFi('WPA2', 'TestNetwork', 'password123', false),
        ];

        foreach ($types as $typeName => $qrType) {
            $filename = $this->tempDir."type_{$typeName}.png";

            // Generate QR code using the type class
            $qrType->setOutfile($filename)->png();

            // Verify file creation
            $this->assertTrue(file_exists($filename), "QR code file not created for type {$typeName}");
            $this->assertTrue(isPng($filename), "File is not PNG for type {$typeName}");

            // Decode and verify it contains expected content structure
            $reader = new QrReader($filename);
            $decodedText = $reader->text();

            $this->assertNotEmpty($decodedText, "Decoded text is empty for type {$typeName}");

            // Verify the decoded content matches what the type class generates
            $this->assertEquals($qrType->getCodeString(), $decodedText, "Decoded text doesn't match type output for {$typeName}");
        }
    }

    public function test_different_output_formats()
    {
        $testText = 'Format Test';

        // Test PNG
        $pngFile = $this->tempDir.'format_test.png';
        \QR_Code\QR_Code::png($testText, $pngFile);
        $this->assertTrue(file_exists($pngFile) && isPng($pngFile));

        // Test SVG
        $svgFile = $this->tempDir.'format_test.svg';
        \QR_Code\QR_Code::svg($testText, $svgFile);
        $this->assertTrue(file_exists($svgFile) && isSvg($svgFile));

        // Test Text format
        $textResult = \QR_Code\QR_Code::text($testText);
        $this->assertIsArray($textResult);
        $this->assertNotEmpty($textResult);

        // Test Raw format
        $rawFile = $this->tempDir.'format_test.txt';
        $rawResult = \QR_Code\QR_Code::raw($testText, $rawFile);
        $this->assertTrue(file_exists($rawFile));
        $this->assertIsArray($rawResult);
    }

    public function test_v_card_generation()
    {
        $person = new \QR_Code\Types\vCard\Person('John', 'Doe', 'Mr.', 'john.doe@example.com');
        $phone = new \QR_Code\Types\vCard\Phone('HOME', '+1-555-123-4567');
        $address = new \QR_Code\Types\vCard\Address('HOME', true, '123 Main St', 'City', 'ST', '12345', 'USA');

        $vcard = new \QR_Code\Types\QR_VCard($person, [$phone], [$address]);

        $filename = $this->tempDir.'vcard_test.png';
        $vcard->setOutfile($filename)->png();

        $this->assertTrue(file_exists($filename));
        $this->assertTrue(isPng($filename));

        // Verify the vCard content structure
        $reader = new QrReader($filename);
        $decodedText = $reader->text();

        $this->assertStringContainsString('BEGIN:VCARD', $decodedText);
        $this->assertStringContainsString('END:VCARD', $decodedText);
        $this->assertStringContainsString('John', $decodedText);
        $this->assertStringContainsString('Doe', $decodedText);
        $this->assertStringContainsString('john.doe@example.com', $decodedText);
    }

    public function test_calendar_event_generation()
    {
        $startTime = new \DateTime('2024-12-25 18:00:00');
        $endTime = new \DateTime('2024-12-25 22:00:00');
        $event = new \QR_Code\Types\QR_CalendarEvent($startTime, $endTime, 'Christmas Dinner');

        $filename = $this->tempDir.'calendar_test.png';
        $event->setOutfile($filename)->png();

        $this->assertTrue(file_exists($filename));
        $this->assertTrue(isPng($filename));

        // Verify the calendar event content structure
        $reader = new QrReader($filename);
        $decodedText = $reader->text();

        $this->assertStringContainsString('BEGIN:VEVENT', $decodedText);
        $this->assertStringContainsString('END:VEVENT', $decodedText);
        $this->assertStringContainsString('Christmas Dinner', $decodedText);
    }

    public function test_error_correction()
    {
        $testText = 'Error Correction Test';
        $levels = ['L', 'M', 'Q', 'H'];

        foreach ($levels as $level) {
            $filename = $this->tempDir."error_correction_{$level}.png";
            \QR_Code\QR_Code::png($testText, $filename, $level);

            $this->assertTrue(file_exists($filename));
            $this->assertTrue(isPng($filename));

            // All should decode to the same text regardless of error correction level
            $reader = new QrReader($filename);
            $decodedText = $reader->text();
            $this->assertEquals($testText, $decodedText);
        }
    }

    public function test_large_data_handling()
    {
        // Test with progressively larger amounts of data
        $baseSizes = [50, 100, 500, 1000];

        foreach ($baseSizes as $size) {
            $largeText = str_repeat('A', $size);
            $filename = $this->tempDir."large_data_{$size}.png";

            try {
                \QR_Code\QR_Code::png($largeText, $filename);

                if (file_exists($filename)) {
                    $this->assertTrue(isPng($filename));

                    // Verify decoding works
                    $reader = new QrReader($filename);
                    $decodedText = $reader->text();
                    $this->assertEquals($largeText, $decodedText);
                }
            } catch (\Exception $e) {
                // It's acceptable for very large data to fail
                if ($size < 500) {
                    throw $e; // Smaller sizes should work
                }
            }
        }
    }
}
