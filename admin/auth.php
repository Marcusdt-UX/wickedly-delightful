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

// Check session lifetime
if ((time() - $_SESSION['logged_in_at']) > SESSION_LIFETIME) {
    session_destroy();
    header('Location: /admin/login.php?expired=1');
    exit;
}

// Make merchant info available
$merchantId   = $_SESSION['square_merchant_id'];
$merchantName = $_SESSION['square_merchant_name'] ?? 'Admin';
$locationId   = $_SESSION['square_location_id'] ?? '';
