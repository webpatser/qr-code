<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use QR_Code\Cache\FileCache;
use QR_Code\Types\QR_Text;
use QR_Code\Util\AbstractGenerator;

class SecurityTest extends TestCase
{
    protected string $cacheDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'qrcode_security_test_'.uniqid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_dir($this->cacheDir)) {
            foreach (glob($this->cacheDir.'/*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->cacheDir);
        }
    }

    public function test_cached_payload_does_not_instantiate_serialized_objects(): void
    {
        $cache = new FileCache($this->cacheDir);

        // Forge a cache file whose 'data' contains a serialized object. A naive
        // unserialize() would instantiate it (object injection); the hardened
        // version must return it as an incomplete class instead.
        $key = 'malicious';
        $file = $this->cacheDir.'/qr_'.preg_replace('/[^a-zA-Z0-9_-]/', '', $key).'.cache';

        $payload = [
            'data' => new \ArrayObject(['injected' => true]),
            'expires' => 0,
            'created' => time(),
        ];
        file_put_contents($file, serialize($payload));

        $result = $cache->get('qr_'.$key);

        $this->assertNotInstanceOf(\ArrayObject::class, $result);
        $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $result);
    }

    public function test_cache_round_trip_still_works_for_plain_values(): void
    {
        $cache = new FileCache($this->cacheDir);

        $this->assertTrue($cache->set('plain', 'hello world'));
        $this->assertSame('hello world', $cache->get('plain'));
    }

    public function test_set_size_rejects_oversized_value(): void
    {
        $generator = new QR_Text('test');

        $this->expectException(\InvalidArgumentException::class);

        $generator->setSize(AbstractGenerator::MAX_SIZE + 1);
    }

    public function test_set_size_rejects_non_positive_value(): void
    {
        $generator = new QR_Text('test');

        $this->expectException(\InvalidArgumentException::class);

        $generator->setSize(0);
    }

    public function test_set_size_accepts_valid_value(): void
    {
        $generator = new QR_Text('test');

        $this->assertSame($generator, $generator->setSize(10));
    }
}
