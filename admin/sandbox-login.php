<?php
/**
 * Sandbox bypass login.
 * Uses the sandbox access token directly — skips OAuth flow.
 * Only works when SQUARE_ENV is 'sandbox'.
 */
session_start();
require_once __DIR__ . '/config.php';

if (SQUARE_ENV !== 'sandbox') {
    http_response_code(403);
    echo 'Sandbox login is only available in sandbox mode.';
    exit;
}

// Verify CSRF token
$token = $_POST['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['sandbox_csrf'] ?? '', $token)) {
    http_response_code(403);
    echo 'Invalid request. <a href="/admin/login.php">Try again</a>';
    exit;
}
unset($_SESSION['sandbox_csrf']);

// Sandbox access token (from Square Developer Console → Sandbox Test Accounts)
$accessToken = 'EAAAl8mHtHzEHM_WLPhbMixT-LL8lPGAUAkFDOG24TCSvCxFeXEhmco7CGh8Qpmz';
$apiBase = 'https://connect.squareupsandbox.com/v2';

// Fetch merchant info
$merchantName = 'Sandbox Merchant';
$merchantId   = '';

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

if (!empty($merchantResp['merchant'])) {
    $merchantName = $merchantResp['merchant']['business_name'] ?? $merchantName;
    $merchantId   = $merchantResp['merchant']['id'] ?? '';
}

// Fetch primary location
$locationId = '';
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

// Save token file so the shop API can use it
save_square_token([
    'access_token'  => $accessToken,
    'refresh_token' => '',
    'merchant_id'   => $merchantId,
    'expires_at'    => time() + 2592000, // sandbox tokens don't expire
    'token_type'    => 'bearer',
    'obtained_at'   => date('c'),
    'location_id'   => $locationId,
]);

// Set session
$_SESSION['square_merchant_id']   = $merchantId;
$_SESSION['square_merchant_name'] = $merchantName;
$_SESSION['square_location_id']   = $locationId;
$_SESSION['logged_in_at']         = time();

header('Location: /admin/');
exit;
