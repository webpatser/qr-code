<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class AdditionalTests extends TestCase
{
    protected $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = __DIR__.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
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

    public function test_qr_code_error_correction_levels()
    {
        $levels = ['L', 'M', 'Q', 'H'];

        foreach ($levels as $level) {
            $filename = $this->tempDir."test_level_{$level}.png";
            $result = \QR_Code\QR_Code::png('Test Level '.$level, $filename, $level);

            $this->assertTrue(file_exists($filename), "File not created for level {$level}");
            $this->assertTrue(isPng($filename), "File is not PNG for level {$level}");
        }
    }

    public function test_qr_code_sizes()
    {
        $sizes = [1, 3, 5, 10];

        foreach ($sizes as $size) {
            $filename = $this->tempDir."test_size_{$size}.png";
            $result = \QR_Code\QR_Code::png('Test Size '.$size, $filename, 'L', $size);

            $this->assertTrue(file_exists($filename), "File not created for size {$size}");
            $this->assertTrue(isPng($filename), "File is not PNG for size {$size}");

            // Larger sizes should create larger files
            if ($size > 1) {
                $prevFilename = $this->tempDir.'test_size_'.($size - 2).'.png';
                if (file_exists($prevFilename)) {
                    $this->assertGreaterThan(
                        filesize($prevFilename),
                        filesize($filename),
                        "Size {$size} should create larger file than previous size"
                    );
                }
            }
        }
    }

    public function test_qr_code_margins()
    {
        $margins = [0, 2, 4, 8];

        foreach ($margins as $margin) {
            $filename = $this->tempDir."test_margin_{$margin}.png";
            $result = \QR_Code\QR_Code::png('Test Margin '.$margin, $filename, 'L', 3, $margin);

            $this->assertTrue(file_exists($filename), "File not created for margin {$margin}");
            $this->assertTrue(isPng($filename), "File is not PNG for margin {$margin}");
        }
    }

    public function test_qr_code_colors()
    {
        $colors = [
            'black_white' => [QR_WHITE, QR_BLACK],
            'red_white' => [QR_WHITE, QR_RED],
            'blue_white' => [QR_WHITE, QR_BLUE],
            'green_white' => [QR_WHITE, QR_GREEN],
        ];

        foreach ($colors as $colorName => $colorPair) {
            $filename = $this->tempDir."test_color_{$colorName}.png";
            $result = \QR_Code\QR_Code::png(
                'Test Color '.$colorName,
                $filename,
                'L',
                3,
                4,
                false,
                $colorPair[0], // back color
                $colorPair[1]  // fore color
            );

            $this->assertTrue(file_exists($filename), "File not created for color {$colorName}");
            $this->assertTrue(isPng($filename), "File is not PNG for color {$colorName}");
        }
    }

    public function test_svg_generation()
    {
        $filename = $this->tempDir.'test.svg';
        \QR_Code\QR_Code::svg('Test SVG', $filename);

        $this->assertTrue(file_exists($filename), 'SVG file not created');
        $this->assertTrue(isSvg($filename), 'File is not SVG');

        $content = file_get_contents($filename);
        $this->assertStringContainsString('<svg', $content, 'SVG content should contain <svg tag');
        $this->assertStringContainsString('</svg>', $content, 'SVG content should contain closing </svg> tag');
    }

    public function test_text_output()
    {
        $result = \QR_Code\QR_Code::text('Test Text Output');

        $this->assertIsArray($result, 'Text output should return array');
        $this->assertNotEmpty($result, 'Text output should not be empty');

        // Text output returns array of strings representing rows, not 2D array
        $this->assertIsString($result[0], 'First element should be string (row)');

        // Check that all rows have the same length
        $rowLength = strlen($result[0]);
        foreach ($result as $row) {
            $this->assertEquals($rowLength, strlen($row), 'All rows should have same length');
        }
    }

    public function test_raw_output()
    {
        $filename = $this->tempDir.'test_raw.txt';
        $result = \QR_Code\QR_Code::raw('Test Raw Output', $filename);

        $this->assertTrue(file_exists($filename), 'Raw file not created');
        $this->assertIsArray($result, 'Raw output should return array');

        $content = file_get_contents($filename);
        $this->assertNotEmpty($content, 'Raw file should have content');
    }

    public function test_long_text_handling()
    {
        // Test with a long string to check encoding limits
        $longText = str_repeat('This is a long text string for QR code testing. ', 20);

        $filename = $this->tempDir.'test_long.png';
        $result = \QR_Code\QR_Code::png($longText, $filename);

        $this->assertTrue(file_exists($filename), 'File not created for long text');
        $this->assertTrue(isPng($filename), 'File is not PNG for long text');
    }

    public function test_special_characters()
    {
        $specialText = 'Special chars: àáâãäåæçèéêë ñóôõöø üýÿ 中文 日本語 한글 🎉👍💯';

        $filename = $this->tempDir.'test_special.png';
        $result = \QR_Code\QR_Code::png($specialText, $filename);

        $this->assertTrue(file_exists($filename), 'File not created for special characters');
        $this->assertTrue(isPng($filename), 'File is not PNG for special characters');
    }

    public function test_empty_string_handling()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('empty string');

        \QR_Code\QR_Code::png('', 'test.png');
    }

    public function test_helper_functions()
    {
        // Test the helper functions directly
        $this->assertTrue(validateErrorCorrectionLevel('L'));
        $this->assertTrue(validateErrorCorrectionLevel('M'));
        $this->assertTrue(validateErrorCorrectionLevel('Q'));
        $this->assertTrue(validateErrorCorrectionLevel('H'));

        $this->assertFalse(validateErrorCorrectionLevel('X'));
        $this->assertFalse(validateErrorCorrectionLevel('LL'));
        $this->assertFalse(validateErrorCorrectionLevel(''));

        $this->assertTrue(startsWith('test', 'testing'));
        $this->assertFalse(startsWith('xyz', 'testing'));

        $this->assertTrue(inString('testing', 'test'));
        $this->assertFalse(inString('testing', 'xyz'));
    }
}
