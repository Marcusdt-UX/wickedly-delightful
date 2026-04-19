<?php
/**
 * Admin Configuration — Square OAuth
 *
 * Get these from: https://developer.squareup.com → Applications → your app
 */

// Square OAuth Application credentials
define('SQUARE_APP_ID', '');        // Application ID (sq0idp-...)
define('SQUARE_APP_SECRET', '');    // Application Secret (sq0csp-...)

// Square environment: 'production' or 'sandbox'
define('SQUARE_ENV', 'production');

// OAuth URLs
define('SQUARE_OAUTH_BASE', SQUARE_ENV === 'production'
    ? 'https://connect.squareup.com'
    : 'https://connect.squareupsandbox.com'
);

// Permissions we request (catalog + inventory + merchant info)
define('SQUARE_OAUTH_SCOPES', implode('+', [
    'ITEMS_READ',
    'INVENTORY_READ',
    'MERCHANT_PROFILE_READ',
    'ORDERS_WRITE',
    'ORDERS_READ',
    'PAYMENTS_WRITE',
    'PAYMENTS_READ',
]));

// File where the OAuth tokens are stored (outside web root ideally, but this works on shared hosting)
define('SQUARE_TOKEN_FILE', __DIR__ . '/../.data/square-token.json');

// Session lifetime (8 hours)
define('SESSION_LIFETIME', 8 * 3600);

/**
 * Read stored Square OAuth token data.
 */
function get_square_token(): ?array {
    if (!file_exists(SQUARE_TOKEN_FILE)) return null;
    $data = json_decode(file_get_contents(SQUARE_TOKEN_FILE), true);
    return $data ?: null;
}

/**
 * Save Square OAuth token data.
 */
function save_square_token(array $data): void {
    $dir = dirname(SQUARE_TOKEN_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(SQUARE_TOKEN_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Build the Square OAuth authorize URL.
 */
function square_authorize_url(string $state): string {
    $protocol = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ? 'https' : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $redirectUri = $protocol . '://' . $host . '/admin/callback.php';

    return SQUARE_OAUTH_BASE . '/oauth2/authorize'
        . '?client_id=' . SQUARE_APP_ID
        . '&scope=' . SQUARE_OAUTH_SCOPES
        . '&session=' . 'false'
        . '&state=' . urlencode($state)
        . '&redirect_uri=' . urlencode($redirectUri);
}

/**
 * Exchange an authorization code for access + refresh tokens.
 */
function square_exchange_code(string $code): array {
    $protocol = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ? 'https' : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $redirectUri = $protocol . '://' . $host . '/admin/callback.php';

    $ch = curl_init(SQUARE_OAUTH_BASE . '/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_POSTFIELDS     => json_encode([
            'client_id'     => SQUARE_APP_ID,
            'client_secret' => SQUARE_APP_SECRET,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectUri,
        ]),
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true) ?: [];
}

/**
 * Refresh an expired access token.
 */
function square_refresh_token(string $refreshToken): array {
    $ch = curl_init(SQUARE_OAUTH_BASE . '/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_POSTFIELDS     => json_encode([
            'client_id'     => SQUARE_APP_ID,
            'client_secret' => SQUARE_APP_SECRET,
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ]),
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true) ?: [];
}

/**
 * Get a valid access token, refreshing if necessary.
 * Returns the token string or null.
 */
function get_valid_access_token(): ?string {
    $token = get_square_token();
    if (!$token || empty($token['access_token'])) return null;

    // Check if token is expired (Square tokens expire after 30 days)
    $expiresAt = $token['expires_at'] ?? 0;
    if ($expiresAt && time() > ($expiresAt - 3600)) {
        // Refresh it
        if (!empty($token['refresh_token'])) {
            $refreshed = square_refresh_token($token['refresh_token']);
            if (!empty($refreshed['access_token'])) {
                $token['access_token']  = $refreshed['access_token'];
                $token['refresh_token'] = $refreshed['refresh_token'] ?? $token['refresh_token'];
                $token['expires_at']    = time() + ($refreshed['expires_at'] ? strtotime($refreshed['expires_at']) - time() : 2592000);
                save_square_token($token);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    return $token['access_token'];
}
