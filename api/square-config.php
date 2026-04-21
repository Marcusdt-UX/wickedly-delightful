<?php
/**
 * Square API Configuration
 * 
 * Reads credentials from the OAuth token file stored by the admin login flow.
 * Falls back to hardcoded values if set (for manual configuration).
 */

// Square environment: 'sandbox' or 'production'
define('SQUARE_ENVIRONMENT', 'production');

// Token file written by admin OAuth flow
define('SQUARE_TOKEN_FILE_PATH', __DIR__ . '/../.data/square-token.json');

// Load OAuth token data if available
$_sqTokenData = file_exists(SQUARE_TOKEN_FILE_PATH)
    ? (json_decode(file_get_contents(SQUARE_TOKEN_FILE_PATH), true) ?: [])
    : [];

// Require token environment to match current environment; this prevents stale sandbox tokens
// from being used after switching to production mode (and vice versa).
if (($_sqTokenData['environment'] ?? '') !== SQUARE_ENVIRONMENT) {
    $_sqTokenData = [];
}

// Access token: prefer OAuth token, fall back to manual
define('SQUARE_ACCESS_TOKEN', $_sqTokenData['access_token'] ?? '');

// Location ID: prefer session-stored value from OAuth, fall back to manual
define('SQUARE_LOCATION_ID', $_sqTokenData['location_id'] ?? '');

// Square API base URLs
define('SQUARE_API_URL', SQUARE_ENVIRONMENT === 'production'
    ? 'https://connect.squareup.com/v2'
    : 'https://connect.squareupsandbox.com/v2'
);

// Square Web Payments SDK base URL
define('SQUARE_WEB_SDK_URL', SQUARE_ENVIRONMENT === 'production'
    ? 'https://web.squarecdn.com/v1/square.js'
    : 'https://sandbox.web.squarecdn.com/v1/square.js'
);

// Square Application ID (for Web Payments SDK)
define('SQUARE_APPLICATION_ID', '');

// Storefront shipping settings for checkout links
define('SHOP_CURRENCY', 'USD');
define('SHOP_DEFAULT_SHIPPING_METHOD', 'standard');
define('SHOP_REQUIRE_SHIPPING_ADDRESS', true);
define('SHOP_SHIPPING_METHODS', [
    'standard' => [
        'label' => 'Standard Shipping (3-5 business days)',
        'amount' => 895,
    ],
    'express' => [
        'label' => 'Express Shipping (1-2 business days)',
        'amount' => 1595,
    ],
    'pickup' => [
        'label' => 'Local Pickup',
        'amount' => 0,
    ],
]);

// Cache duration in seconds (5 minutes)
define('SQUARE_CACHE_TTL', 300);

// Cache directory
define('SQUARE_CACHE_DIR', __DIR__ . '/../.cache');

/**
 * Make an authenticated request to the Square API.
 */
function square_request(string $endpoint, string $method = 'GET', ?array $body = null): array {
    $url = SQUARE_API_URL . $endpoint;

    $headers = [
        'Authorization: Bearer ' . SQUARE_ACCESS_TOKEN,
        'Content-Type: application/json',
        'Square-Version: 2025-01-23',
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 15,
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['error' => 'Connection failed: ' . $error];
    }

    $data = json_decode($response, true);

    if ($httpCode >= 400) {
        $msg = $data['errors'][0]['detail'] ?? 'Unknown Square API error';
        return ['error' => $msg, 'http_code' => $httpCode];
    }

    return $data ?? [];
}

/**
 * Simple file-based cache for API responses.
 */
function square_cache_get(string $key): ?array {
    $file = SQUARE_CACHE_DIR . '/' . md5($key) . '.json';
    if (!file_exists($file)) return null;

    $data = json_decode(file_get_contents($file), true);
    if (!$data || ($data['expires'] ?? 0) < time()) {
        @unlink($file);
        return null;
    }

    return $data['payload'];
}

function square_cache_set(string $key, array $payload): void {
    if (!is_dir(SQUARE_CACHE_DIR)) {
        mkdir(SQUARE_CACHE_DIR, 0755, true);
    }

    $file = SQUARE_CACHE_DIR . '/' . md5($key) . '.json';
    file_put_contents($file, json_encode([
        'expires' => time() + SQUARE_CACHE_TTL,
        'payload' => $payload,
    ]));
}
