<?php
/**
 * Creates a Square Checkout link for the given cart items.
 * Expects POST with JSON body: { items: [{ variation_id, quantity }] }
 */
header('Content-Type: application/json');

require_once __DIR__ . '/square-config.php';

function square_checkout_uuid(string $prefix): string {
    return $prefix . '-' . bin2hex(random_bytes(8));
}

function square_checkout_iso_timestamp(string $modifier = '+2 business days'): string {
    try {
        $date = new DateTimeImmutable($modifier, new DateTimeZone('UTC'));
    } catch (Exception $exception) {
        $date = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    return $date->format('Y-m-d\TH:i:s\Z');
}

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
$shippingMethod = strtolower(trim((string)($input['shipping_method'] ?? SHOP_DEFAULT_SHIPPING_METHOD)));

$shippingMethods = defined('SHOP_SHIPPING_METHODS') ? SHOP_SHIPPING_METHODS : [];
if (!$shippingMethods) {
    $shippingMethods = [
        'standard' => ['label' => 'Standard Shipping', 'amount' => 895],
        'express' => ['label' => 'Express Shipping', 'amount' => 1595],
        'pickup' => ['label' => 'Local Pickup', 'amount' => 0],
    ];
}

if (!isset($shippingMethods[$shippingMethod])) {
    $shippingMethod = defined('SHOP_DEFAULT_SHIPPING_METHOD') ? SHOP_DEFAULT_SHIPPING_METHOD : 'standard';
}

$shippingLabel = $shippingMethods[$shippingMethod]['label'] ?? 'Shipping';
$shippingAmount = max(0, intval($shippingMethods[$shippingMethod]['amount'] ?? 0));
$currency = defined('SHOP_CURRENCY') ? SHOP_CURRENCY : 'USD';

if (empty($cartItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// Build order line items
$lineItems = [];
$requestedByVariation = [];
foreach ($cartItems as $ci) {
    $variationId = $ci['variation_id'] ?? '';
    $quantity = max(1, intval($ci['quantity'] ?? 1));

    if (!$variationId || !preg_match('/^[A-Z0-9_-]+$/i', $variationId)) {
        continue;
    }

    $lineItems[] = [
        'uid'              => square_checkout_uuid('line'),
        'quantity'         => (string) $quantity,
        'catalog_object_id' => $variationId,
    ];

    if (!isset($requestedByVariation[$variationId])) {
        $requestedByVariation[$variationId] = 0;
    }
    $requestedByVariation[$variationId] += $quantity;
}

if (empty($lineItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid items']);
    exit;
}

// Validate requested quantities against live inventory to prevent overselling.
$inventory = square_request('/inventory/counts/batch-retrieve', 'POST', [
    'catalog_object_ids' => array_keys($requestedByVariation),
    'location_ids' => [SQUARE_LOCATION_ID],
]);

if (isset($inventory['error'])) {
    http_response_code(502);
    echo json_encode(['error' => $inventory['error']]);
    exit;
}

$availableByVariation = [];
foreach ($inventory['counts'] ?? [] as $count) {
    $availableByVariation[$count['catalog_object_id']] = (float) ($count['quantity'] ?? 0);
}

$insufficient = [];
foreach ($requestedByVariation as $variationId => $requestedQty) {
    $availableQty = (int) floor($availableByVariation[$variationId] ?? 0);
    if ($requestedQty > $availableQty) {
        $insufficient[] = [
            'variation_id' => $variationId,
            'requested' => $requestedQty,
            'available' => $availableQty,
        ];
    }
}

if (!empty($insufficient)) {
    http_response_code(409);
    echo json_encode([
        'error' => 'Some items are no longer available in the requested quantity. Please update your cart and try again.',
        'insufficient_items' => $insufficient,
    ]);
    exit;
}

// Determine return URL
$protocol = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ? 'https' : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
$host = $_SERVER['HTTP_HOST'] ?? 'wickedlydelightfulscents.shop';
$returnUrl = $protocol . '://' . $host . '/shop.php?checkout=complete';

// Create checkout via Square Payment Links API
$idempotencyKey = bin2hex(random_bytes(16));

$order = [
    'location_id' => SQUARE_LOCATION_ID,
    'line_items'  => $lineItems,
    'metadata'    => [
        'shipping_method' => $shippingMethod,
        'shipping_amount' => (string) $shippingAmount,
    ],
];

if ($shippingMethod === 'pickup') {
    $order['fulfillments'] = [[
        'uid' => square_checkout_uuid('pickup'),
        'type' => 'PICKUP',
        'state' => 'PROPOSED',
        'pickup_details' => [
            'schedule_type' => 'ASAP',
            'note' => 'Local pickup selected on storefront checkout.',
        ],
    ]];
} else {
    $order['fulfillments'] = [[
        'uid' => square_checkout_uuid('shipment'),
        'type' => 'SHIPMENT',
        'state' => 'PROPOSED',
        'shipment_details' => [
            'shipping_type' => $shippingLabel,
            'expected_shipped_at' => square_checkout_iso_timestamp(
                $shippingMethod === 'express' ? '+1 business day' : '+3 business days'
            ),
        ],
    ]];
}

$payload = [
    'idempotency_key' => $idempotencyKey,
    'order' => $order,
    'checkout_options' => [
        'redirect_url'                => $returnUrl,
        'allow_tipping'               => false,
        'enable_coupon'               => false,
        'enable_loyalty'              => false,
        'ask_for_shipping_address'    => (defined('SHOP_REQUIRE_SHIPPING_ADDRESS') ? SHOP_REQUIRE_SHIPPING_ADDRESS : true) && $shippingMethod !== 'pickup',
    ],
];

if ($shippingAmount > 0) {
    $payload['order']['service_charges'] = [[
        'uid' => square_checkout_uuid('shipping'),
        'name' => $shippingLabel,
        'amount_money' => [
            'amount' => $shippingAmount,
            'currency' => $currency,
        ],
        'calculation_phase' => 'SUBTOTAL_PHASE',
        'taxable' => false,
    ]];
}

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
    'shipping_method' => $shippingMethod,
    'shipping_amount' => $shippingAmount,
]);
