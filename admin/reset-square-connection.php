<?php
/**
 * Reset Square connection.
 * Clears saved token + merchant lock so a new Square account can authorize.
 */
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed.';
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!$csrf || !hash_equals($_SESSION['admin_reset_csrf'] ?? '', $csrf)) {
    http_response_code(403);
    echo 'Invalid request. <a href="/admin/">Back to dashboard</a>';
    exit;
}

clear_square_connection();

// End current admin session so next login re-authorizes cleanly.
session_unset();
session_destroy();

header('Location: /admin/login.php?reset=1');
exit;
