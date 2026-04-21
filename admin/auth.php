<?php
/**
 * Auth guard — include at top of any protected admin page.
 * Redirects to login if not authenticated.
 */
session_start();

require_once __DIR__ . '/config.php';

// Check session
if (empty($_SESSION['square_merchant_id']) || empty($_SESSION['logged_in_at'])) {
    header('Location: /admin/login.php');
    exit;
}

$sessionMerchantId = (string)$_SESSION['square_merchant_id'];
$lockedMerchantId = get_locked_merchant_id();
if ($lockedMerchantId && $sessionMerchantId !== $lockedMerchantId) {
    session_destroy();
    header('Location: /admin/login.php?unauthorized=1');
    exit;
}

// Check session lifetime
if ((time() - $_SESSION['logged_in_at']) > SESSION_LIFETIME) {
    session_destroy();
    header('Location: /admin/login.php?expired=1');
    exit;
}

// Make merchant info available
$merchantId   = $sessionMerchantId;
$merchantName = $_SESSION['square_merchant_name'] ?? 'Admin';
$locationId   = $_SESSION['square_location_id'] ?? '';
