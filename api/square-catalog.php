<?php
/**
 * Square Catalog + Inventory API proxy.
 * Returns JSON with products, prices, images, and stock counts.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/square-catalog-data.php';

if (!SQUARE_ACCESS_TOKEN) {
    http_response_code(500);
    echo json_encode(['error' => 'Square API not configured']);
    exit;
}
$result = square_get_catalog_products(isset($_GET['nocache']));

if (isset($result['error'])) {
    http_response_code(502);
    echo json_encode(['error' => $result['error']]);
    exit;
}

$lookup = trim((string)($_GET['item'] ?? $_GET['id'] ?? ''));
if ($lookup !== '') {
    $product = square_find_catalog_product($result['products'] ?? [], $lookup);
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }
    echo json_encode(['product' => $product]);
    exit;
}

echo json_encode($result);
