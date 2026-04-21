<?php
/**
 * Square OAuth callback handler.
 * Square redirects here after the user authorizes.
 */
session_start();
require_once __DIR__ . '/config.php';

// Verify state to prevent CSRF
$state = $_GET['state'] ?? '';
if (!$state || !hash_equals($_SESSION['oauth_state'] ?? '', $state)) {
    http_response_code(403);
    echo 'Invalid state parameter. <a href="/admin/login.php">Try again</a>';
    exit;
}
unset($_SESSION['oauth_state']);

// Check for errors from Square
if (!empty($_GET['error'])) {
    $desc = htmlspecialchars($_GET['error_description'] ?? $_GET['error']);
    echo 'Authorization denied: ' . $desc . '. <a href="/admin/login.php">Try again</a>';
    exit;
}

// Exchange authorization code for tokens
$code = $_GET['code'] ?? '';
if (!$code) {
    echo 'No authorization code received. <a href="/admin/login.php">Try again</a>';
    exit;
}

$tokenData = square_exchange_code($code);

if (empty($tokenData['access_token'])) {
    $err = $tokenData['message'] ?? $tokenData['error_description'] ?? 'Unknown error';
    echo 'Token exchange failed: ' . htmlspecialchars($err) . '. <a href="/admin/login.php">Try again</a>';
    exit;
}

$oauthMerchantId = trim((string)($tokenData['merchant_id'] ?? ''));
if ($oauthMerchantId === '') {
    echo 'Could not determine Square merchant ID. <a href="/admin/login.php">Try again</a>';
    exit;
}

// Merchant lock enforcement: once locked, only that Square account can authorize.
$lockedMerchantId = get_locked_merchant_id();
if ($lockedMerchantId && $lockedMerchantId !== $oauthMerchantId) {
    http_response_code(403);
    echo 'This admin is locked to a different Square account. Please contact support. <a href="/admin/login.php">Back to login</a>';
    exit;
}

// Calculate token expiry
$expiresAt = !empty($tokenData['expires_at'])
    ? strtotime($tokenData['expires_at'])
    : time() + 2592000; // 30 days default

// Save token to file
save_square_token([
    'access_token'  => $tokenData['access_token'],
    'refresh_token' => $tokenData['refresh_token'] ?? '',
    'merchant_id'   => $oauthMerchantId,
    'expires_at'    => $expiresAt,
    'token_type'    => $tokenData['token_type'] ?? 'bearer',
    'environment'   => SQUARE_ENV,
    'obtained_at'   => date('c'),
    'location_id'   => '', // populated below after fetching locations
]);

// First successful production login binds the admin to this merchant account.
if (!$lockedMerchantId) {
    set_locked_merchant_id($oauthMerchantId);
}

// Create owner recovery key on first successful setup.
if (!has_recovery_key()) {
    $_SESSION['new_recovery_key'] = generate_recovery_key();
}

// Fetch merchant info for the session
$merchantName = 'Admin';
$locationId = '';

$accessToken = $tokenData['access_token'];
$apiBase = SQUARE_ENV === 'production'
    ? 'https://connect.squareup.com/v2'
    : 'https://connect.squareupsandbox.com/v2';

// Get merchant name
$ch = curl_init($apiBase . '/merchants/me');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
]);
$merchantResp = json_decode(curl_exec($ch), true);
curl_close($ch);
if (!empty($merchantResp['merchant']['business_name'])) {
    $merchantName = $merchantResp['merchant']['business_name'];
}

// Get primary location
$ch = curl_init($apiBase . '/locations');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
]);
$locResp = json_decode(curl_exec($ch), true);
curl_close($ch);
foreach ($locResp['locations'] ?? [] as $loc) {
    if (($loc['status'] ?? '') === 'ACTIVE') {
        $locationId = $loc['id'];
        break;
    }
}

// Update token file with location_id so the shop API can use it
if ($locationId) {
    $storedToken = get_square_token();
    if ($storedToken) {
        $storedToken['location_id'] = $locationId;
        save_square_token($storedToken);
    }
}

// Set session
$_SESSION['square_merchant_id']   = $oauthMerchantId;
$_SESSION['square_merchant_name'] = $merchantName;
$_SESSION['square_location_id']   = $locationId;
$_SESSION['logged_in_at']         = time();

// Redirect to admin dashboard
header('Location: /admin/');
exit;
