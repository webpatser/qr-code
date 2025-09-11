<?php

namespace QR_Code\Performance;

use QR_Code\Cache\FileCache;
use QR_Code\Cache\MemoryCache;
use QR_Code\Enums\ImageEngine;
use QR_Code\FastQRCode;
use QR_Code\QR_Code;

/**
 * Performance benchmarking tool for QR code generation
 */
class Benchmark
{
    private array $results = [];

    /**
     * Run comprehensive benchmark
     */
    public function runBenchmark(): array
    {
        echo "🚀 Running QR Code Performance Benchmark...\n\n";

        $testData = [
            'short' => 'Hello World!',
            'medium' => str_repeat('Performance test data ', 10),
            'long' => str_repeat('Long performance test with lots of data ', 50),
            'url' => 'https://github.com/christoph/qr-code/performance/benchmark/test',
            'email' => 'mailto:test@example.com?subject=Performance%20Test&body=Benchmark%20email',
        ];

        // Test original vs optimized
        $this->benchmarkOriginalVsOptimized($testData);

        // Test different engines
        $this->benchmarkEngines($testData);

        // Test caching performance
        $this->benchmarkCaching($testData);

        // Test batch operations
        $this->benchmarkBatch($testData);

        return $this->results;
    }

    /**
     * Benchmark original vs optimized implementation
     */
    private function benchmarkOriginalVsOptimized(array $testData): void
    {
        echo "📊 Original vs Optimized Implementation\n";
        echo str_repeat('-', 50)."\n";

        foreach ($testData as $name => $data) {
            // Original implementation
            $originalTime = $this->measureTime(function () use ($data) {
                return QR_Code::png($data);
            });

            // Optimized implementation
            $fastQR = new FastQRCode;
            $optimizedTime = $this->measureTime(function () use ($fastQR, $data) {
                return $fastQR->png($data);
            });

            $improvement = (($originalTime - $optimizedTime) / $originalTime) * 100;

            echo sprintf(
                "%-10s | Original: %6.2fms | Optimized: %6.2fms | Improvement: %+5.1f%%\n",
                $name,
                $originalTime * 1000,
                $optimizedTime * 1000,
                $improvement
            );

            $this->results['original_vs_optimized'][$name] = [
                'original_time' => $originalTime,
                'optimized_time' => $optimizedTime,
                'improvement_percent' => $improvement,
            ];
        }
        echo "\n";
    }

    /**
     * Benchmark different image engines
     */
    private function benchmarkEngines(array $testData): void
    {
        echo "🖼️  Image Engine Performance\n";
        echo str_repeat('-', 50)."\n";

        $engines = ImageEngine::getAvailable();
        $testData = ['medium' => $testData['medium']]; // Use medium test data

        foreach ($engines as $engine) {
            if ($engine === ImageEngine::None) {
                continue;
            }

            echo "Engine: {$engine->value}\n";

            $fastQR = new FastQRCode(enableCache: false);
            $fastQR->setImageEngine($engine);

            $times = [];
            for ($i = 0; $i < 5; $i++) {
                $times[] = $this->measureTime(function () use ($fastQR, $testData) {
                    return $fastQR->png($testData['medium']);
                });
            }

            $avgTime = array_sum($times) / count($times);
            $minTime = min($times);
            $maxTime = max($times);

            echo sprintf(
                "  Avg: %6.2fms | Min: %6.2fms | Max: %6.2fms | Score: %d\n",
                $avgTime * 1000,
                $minTime * 1000,
                $maxTime * 1000,
                $engine->getPerformanceScore()
            );

            $this->results['engines'][$engine->value] = [
                'avg_time' => $avgTime,
                'min_time' => $minTime,
                'max_time' => $maxTime,
                'performance_score' => $engine->getPerformanceScore(),
            ];
        }
        echo "\n";
    }

    /**
     * Benchmark caching performance
     */
    private function benchmarkCaching(array $testData): void
    {
        echo "💾 Caching Performance\n";
        echo str_repeat('-', 50)."\n";

        $data = $testData['medium'];

        // No cache
        $fastQRNoCache = new FastQRCode(enableCache: false);
        $noCacheTime = $this->measureTime(function () use ($fastQRNoCache, $data) {
            return $fastQRNoCache->png($data);
        });

        // Memory cache - first time (cache miss)
        $fastQRMemory = new FastQRCode(new MemoryCache);
        $memoryCacheMissTime = $this->measureTime(function () use ($fastQRMemory, $data) {
            return $fastQRMemory->png($data);
        });

        // Memory cache - second time (cache hit)
        $memoryCacheHitTime = $this->measureTime(function () use ($fastQRMemory, $data) {
            return $fastQRMemory->png($data);
        });

        // File cache - first time (cache miss)
        $fastQRFile = new FastQRCode(new FileCache);
        $fileCacheMissTime = $this->measureTime(function () use ($fastQRFile, $data) {
            return $fastQRFile->png($data);
        });

        // File cache - second time (cache hit)
        $fileCacheHitTime = $this->measureTime(function () use ($fastQRFile, $data) {
            return $fastQRFile->png($data);
        });

        echo sprintf("No Cache:           %6.2fms\n", $noCacheTime * 1000);
        echo sprintf("Memory Cache Miss:  %6.2fms\n", $memoryCacheMissTime * 1000);
        echo sprintf("Memory Cache Hit:   %6.2fms (%.1fx faster)\n",
            $memoryCacheHitTime * 1000,
            $noCacheTime / $memoryCacheHitTime
        );
        echo sprintf("File Cache Miss:    %6.2fms\n", $fileCacheMissTime * 1000);
        echo sprintf("File Cache Hit:     %6.2fms (%.1fx faster)\n",
            $fileCacheHitTime * 1000,
            $noCacheTime / $fileCacheHitTime
        );

        $this->results['caching'] = [
            'no_cache' => $noCacheTime,
            'memory_cache_miss' => $memoryCacheMissTime,
            'memory_cache_hit' => $memoryCacheHitTime,
            'file_cache_miss' => $fileCacheMissTime,
            'file_cache_hit' => $fileCacheHitTime,
        ];

        echo "\n";
    }

    /**
     * Benchmark batch operations
     */
    private function benchmarkBatch(array $testData): void
    {
        echo "📦 Batch Operation Performance\n";
        echo str_repeat('-', 50)."\n";

        $batchData = array_fill(0, 10, $testData['medium']);

        // Individual generation
        $fastQR = new FastQRCode;
        $individualTime = $this->measureTime(function () use ($fastQR, $batchData) {
            foreach ($batchData as $data) {
                $fastQR->png($data);
            }
        });

        // Batch generation
        $batchTime = $this->measureTime(function () use ($fastQR, $batchData) {
            $fastQR->batch($batchData);
        });

        $improvement = (($individualTime - $batchTime) / $individualTime) * 100;

        echo sprintf("Individual (10x):   %6.2fms\n", $individualTime * 1000);
        echo sprintf("Batch (10x):        %6.2fms (%.1fx faster)\n",
            $batchTime * 1000,
            $individualTime / $batchTime
        );

        $this->results['batch'] = [
            'individual_time' => $individualTime,
            'batch_time' => $batchTime,
            'improvement_percent' => $improvement,
        ];

        echo "\n";
    }

    /**
     * Measure execution time
     */
    private function measureTime(callable $callback): float
    {
        $start = microtime(true);
        $callback();

        return microtime(true) - $start;
    }

    /**
     * Get benchmark results
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Generate performance report
     */
    public function generateReport(): string
    {
        $report = "# QR Code Performance Benchmark Report\n\n";

        if (isset($this->results['original_vs_optimized'])) {
            $report .= "## Original vs Optimized Implementation\n\n";
            foreach ($this->results['original_vs_optimized'] as $test => $data) {
                $report .= sprintf(
                    "- **%s**: %.1f%% improvement (%.2fms → %.2fms)\n",
                    ucfirst($test),
                    $data['improvement_percent'],
                    $data['original_time'] * 1000,
                    $data['optimized_time'] * 1000
                );
            }
            $report .= "\n";
        }

        if (isset($this->results['engines'])) {
            $report .= "## Image Engine Performance\n\n";
            foreach ($this->results['engines'] as $engine => $data) {
                $report .= sprintf(
                    "- **%s**: %.2fms average (Score: %d)\n",
                    ucfirst($engine),
                    $data['avg_time'] * 1000,
                    $data['performance_score']
                );
            }
            $report .= "\n";
        }

        return $report;
    }
}
