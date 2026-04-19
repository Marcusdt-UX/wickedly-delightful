<?php
/**
 * Creates a Square Checkout link for the given cart items.
 * Expects POST with JSON body: { items: [{ variation_id, quantity }] }
 */
header('Content-Type: application/json');

require_once __DIR__ . '/square-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

if (!SQUARE_ACCESS_TOKEN || !SQUARE_LOCATION_ID) {
    http_response_code(500);
    echo json_encode(['error' => 'Square API not configured']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cartItems = $input['items'] ?? [];

if (empty($cartItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// Build order line items
$lineItems = [];
foreach ($cartItems as $ci) {
    $variationId = $ci['variation_id'] ?? '';
    $quantity = max(1, intval($ci['quantity'] ?? 1));

    if (!$variationId || !preg_match('/^[A-Z0-9_-]+$/i', $variationId)) {
        continue;
    }

    $lineItems[] = [
        'quantity'         => (string) $quantity,
        'catalog_object_id' => $variationId,
    ];
}

if (empty($lineItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid items']);
    exit;
}

// Determine return URL
$protocol = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ? 'https' : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
$host = $_SERVER['HTTP_HOST'] ?? 'wickedlydelightfulscents.shop';
$returnUrl = $protocol . '://' . $host . '/shop.php?checkout=complete';

// Create checkout via Square Payment Links API
$idempotencyKey = bin2hex(random_bytes(16));

$payload = [
    'idempotency_key' => $idempotencyKey,
    'order' => [
        'location_id' => SQUARE_LOCATION_ID,
        'line_items'  => $lineItems,
    ],
    'checkout_options' => [
        'redirect_url'                => $returnUrl,
        'allow_tipping'               => false,
        'enable_coupon'               => false,
        'enable_loyalty'              => false,
    ],
];

$result = square_request('/online-checkout/payment-links', 'POST', $payload);

if (isset($result['error'])) {
    http_response_code(502);
    echo json_encode(['error' => $result['error']]);
    exit;
}

$link = $result['payment_link'] ?? [];
$checkoutUrl = $link['long_url'] ?? $link['url'] ?? '';

if (!$checkoutUrl) {
    http_response_code(502);
    echo json_encode(['error' => 'No checkout URL returned']);
    exit;
}

echo json_encode([
    'checkout_url' => $checkoutUrl,
    'order_id'     => $link['order_id'] ?? '',
]);
