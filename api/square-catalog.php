<?php
/**
 * Square Catalog + Inventory API proxy.
 * Returns JSON with products, prices, images, and stock counts.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/square-config.php';

if (!SQUARE_ACCESS_TOKEN) {
    http_response_code(500);
    echo json_encode(['error' => 'Square API not configured']);
    exit;
}

// Check cache first (skip with ?nocache)
if (!isset($_GET['nocache'])) {
    $cached = square_cache_get('catalog_products');
    if ($cached) {
        echo json_encode($cached);
        exit;
    }
}

// 1. Fetch catalog items
$catalog = square_request('/catalog/list?types=ITEM,IMAGE');

if (isset($catalog['error'])) {
    http_response_code(502);
    echo json_encode(['error' => $catalog['error']]);
    exit;
}

$objects = $catalog['objects'] ?? [];

// Build image lookup: image_id → url
$images = [];
foreach ($objects as $obj) {
    if ($obj['type'] === 'IMAGE') {
        $images[$obj['id']] = $obj['image_data']['url'] ?? '';
    }
}

// Build items list
$items = [];
$variationIds = [];

foreach ($objects as $obj) {
    if ($obj['type'] !== 'ITEM') continue;
    $item = $obj['item_data'];

    $variations = [];
    foreach ($item['variations'] ?? [] as $v) {
        $vid = $v['id'];
        $vdata = $v['item_variation_data'];
        $variationIds[] = $vid;

        $priceMoney = $vdata['price_money'] ?? null;
        $price = $priceMoney ? intval($priceMoney['amount']) : 0;

        $variations[] = [
            'id'       => $vid,
            'name'     => $vdata['name'] ?? 'Regular',
            'price'    => $price,      // in cents
            'currency' => $priceMoney['currency'] ?? 'USD',
            'sku'      => $vdata['sku'] ?? '',
        ];
    }

    // Primary image
    $imageUrl = '';
    $imageIds = $item['image_ids'] ?? ($obj['image_id'] ? [$obj['image_id']] : []);
    foreach ($imageIds as $imgId) {
        if (isset($images[$imgId])) {
            $imageUrl = $images[$imgId];
            break;
        }
    }

    $items[] = [
        'id'          => $obj['id'],
        'name'        => $item['name'] ?? '',
        'description' => $item['description'] ?? $item['description_html'] ?? '',
        'image'       => $imageUrl,
        'category'    => $item['category']['name'] ?? '',
        'variations'  => $variations,
    ];
}

// 2. Fetch inventory counts for all variations
if (!empty($variationIds)) {
    $inventory = square_request('/inventory/counts/batch-retrieve', 'POST', [
        'catalog_object_ids' => $variationIds,
        'location_ids'       => [SQUARE_LOCATION_ID],
    ]);

    $stockMap = [];
    foreach ($inventory['counts'] ?? [] as $count) {
        $stockMap[$count['catalog_object_id']] = (float) ($count['quantity'] ?? 0);
    }

    // Attach stock to each variation
    foreach ($items as &$item) {
        foreach ($item['variations'] as &$v) {
            $v['stock'] = $stockMap[$v['id']] ?? 0;
            $v['in_stock'] = $v['stock'] > 0;
        }
    }
    unset($item, $v);
}

// Sort alphabetically by name
usort($items, fn($a, $b) => strcmp($a['name'], $b['name']));

$result = ['products' => $items];

// Cache result
square_cache_set('catalog_products', $result);

echo json_encode($result);
