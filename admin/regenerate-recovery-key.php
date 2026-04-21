<?php
/**
 * Regenerate owner recovery key (authenticated admin action).
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

$_SESSION['new_recovery_key'] = generate_recovery_key();
header('Location: /admin/?recovery=rotated');
exit;
