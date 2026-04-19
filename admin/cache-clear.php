<?php
/**
 * Clear the product cache and redirect back to dashboard.
 */
require_once __DIR__ . '/auth.php';

$cacheDir = __DIR__ . '/../.cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.json') as $file) {
        @unlink($file);
    }
}

header('Location: /admin/?cache_cleared=1');
exit;
