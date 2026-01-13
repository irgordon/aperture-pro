<?php

// memory_benchmark.php

function benchmark_current_implementation($filePath) {
    $startMemory = memory_get_peak_usage(true);

    // Simulate the logic in ImageKitAdapter::store
    if (!file_exists($filePath)) {
        throw new Exception("File not found");
    }

    $fileContent = file_get_contents($filePath);
    if ($fileContent === false) {
        throw new Exception("Failed to read file");
    }

    // Use base64 for reliable transmission via WP remote post
    $base64File = base64_encode($fileContent);

    $payload = '';
    $boundary = '--------------------------' . microtime(true);

    // file
    $payload .= "--" . $boundary . "\r\n";
    $payload .= 'Content-Disposition: form-data; name="file"' . "\r\n\r\n";
    $payload .= $base64File . "\r\n";

    // ... other fields would be added here ...

    $payload .= "--" . $boundary . "--\r\n";

    // Simulate passing to wp_remote_post (which would keep it in memory)
    $args = ['body' => $payload];

    $endMemory = memory_get_peak_usage(true);

    return $endMemory - $startMemory;
}

function benchmark_optimized_implementation($filePath) {
    $startMemory = memory_get_peak_usage(true);

    // Optimized approach using CURLFile (simulation)
    if (!file_exists($filePath)) {
        throw new Exception("File not found");
    }

    // We don't read the file content into a variable.
    // We create a CURLFile object.
    $cFile = new CURLFile($filePath);

    $postFields = [
        'file' => $cFile,
        'fileName' => basename($filePath),
        'useUniqueFileName' => 'false',
    ];

    // In real implementation, we would pass this to curl_setopt(..., CURLOPT_POSTFIELDS, $postFields)
    // The memory usage should be minimal as the file is not read into PHP memory.

    $endMemory = memory_get_peak_usage(true);

    return $endMemory - $startMemory;
}

// Create a dummy file (e.g. 5MB)
$tempFile = __DIR__ . '/temp_large_file.dat';
$size = 5 * 1024 * 1024; // 5MB
$fp = fopen($tempFile, 'w');
// Write in chunks to avoid memory spike during creation
$chunk = str_repeat('0', 1024 * 1024);
for ($i = 0; $i < 5; $i++) {
    fwrite($fp, $chunk);
}
fclose($fp);

echo "Benchmark with " . ($size / 1024 / 1024) . "MB file:\n";

try {
    // Force GC
    gc_collect_cycles();
    $memUsageLegacy = benchmark_current_implementation($tempFile);
    echo "Legacy Implementation Memory Usage: " . number_format($memUsageLegacy / 1024 / 1024, 2) . " MB\n";

    // Force GC
    gc_collect_cycles();
    $memUsageOptimized = benchmark_optimized_implementation($tempFile);
    echo "Optimized Implementation Memory Usage: " . number_format($memUsageOptimized / 1024 / 1024, 2) . " MB\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Cleanup
if (file_exists($tempFile)) {
    unlink($tempFile);
}
