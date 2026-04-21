<?php
/**
 * Shared Square catalog data helpers for storefront pages and API endpoints.
 */

require_once __DIR__ . '/square-config.php';

/**
 * Create URL-safe product slug.
 */
function square_slugify(string $value): string {
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');
    return $value !== '' ? $value : 'item';
}

/**
 * Build and cache normalized storefront product data from Square Catalog + Inventory.
 */
function square_get_catalog_products(bool $nocache = false): array {
    if (!SQUARE_ACCESS_TOKEN) {
        return ['error' => 'Square API not configured'];
    }

    if (!$nocache) {
        $cached = square_cache_get('catalog_products');
        if ($cached && is_array($cached)) {
            return $cached;
        }
    }

    $catalog = square_request('/catalog/list?types=ITEM,IMAGE,CATEGORY');
    if (isset($catalog['error'])) {
        return ['error' => $catalog['error']];
    }

    $objects = $catalog['objects'] ?? [];

    $images = [];
    $categories = [];
    foreach ($objects as $obj) {
        $type = $obj['type'] ?? '';

        if ($type === 'IMAGE') {
            $images[$obj['id']] = $obj['image_data']['url'] ?? '';
            continue;
        }

        if ($type === 'CATEGORY') {
            $categories[$obj['id']] = $obj['category_data']['name'] ?? '';
        }
    }

    $items = [];
    $variationIds = [];
    $usedSlugs = [];

    foreach ($objects as $obj) {
        if (($obj['type'] ?? '') !== 'ITEM') {
            continue;
        }

        if (!empty($obj['is_deleted'])) {
            continue;
        }

        $item = $obj['item_data'] ?? [];
        $itemName = (string)($item['name'] ?? 'Untitled Item');

        $variations = [];
        foreach ($item['variations'] ?? [] as $v) {
            if (!empty($v['is_deleted'])) {
                continue;
            }

            $vid = (string)($v['id'] ?? '');
            if ($vid === '') {
                continue;
            }

            $vdata = $v['item_variation_data'] ?? [];
            $priceMoney = $vdata['price_money'] ?? null;
            $price = $priceMoney ? intval($priceMoney['amount'] ?? 0) : 0;

            $variations[] = [
                'id'       => $vid,
                'name'     => $vdata['name'] ?? 'Regular',
                'price'    => $price,
                'currency' => $priceMoney['currency'] ?? 'USD',
                'sku'      => $vdata['sku'] ?? '',
            ];
            $variationIds[] = $vid;
        }

        if (empty($variations)) {
            continue;
        }

        $imageUrls = [];
        $imageIds = $item['image_ids'] ?? (isset($obj['image_id']) ? [$obj['image_id']] : []);
        foreach ($imageIds as $imgId) {
            if (!empty($images[$imgId])) {
                $imageUrls[] = $images[$imgId];
            }
        }

        $baseSlug = square_slugify((string)($item['abbreviation'] ?? $item['name'] ?? $obj['id']));
        $slug = $baseSlug;
        $slugCount = 2;
        while (isset($usedSlugs[$slug])) {
            $slug = $baseSlug . '-' . $slugCount;
            $slugCount++;
        }
        $usedSlugs[$slug] = true;

        $categoryId = (string)($item['category_id'] ?? '');
        $categoryName = $categories[$categoryId] ?? '';

        $prices = array_map(fn($variation) => intval($variation['price'] ?? 0), $variations);
        sort($prices);

        $items[] = [
            'id'            => $obj['id'],
            'slug'          => $slug,
            'name'          => $itemName,
            'description'   => $item['description'] ?? $item['description_html'] ?? '',
            'image'         => $imageUrls[0] ?? '',
            'images'        => $imageUrls,
            'category_id'   => $categoryId,
            'category'      => $categoryName,
            'price_min'     => $prices[0] ?? 0,
            'price_max'     => $prices[count($prices) - 1] ?? 0,
            'variations'    => $variations,
            'product_url'   => '/product.php?item=' . rawurlencode($slug),
        ];
    }

    if (!empty($variationIds)) {
        $inventory = square_request('/inventory/counts/batch-retrieve', 'POST', [
            'catalog_object_ids' => $variationIds,
            'location_ids'       => [SQUARE_LOCATION_ID],
        ]);

        if (!isset($inventory['error'])) {
            $stockMap = [];
            foreach ($inventory['counts'] ?? [] as $count) {
                $stockMap[$count['catalog_object_id']] = (float)($count['quantity'] ?? 0);
            }

            foreach ($items as &$item) {
                foreach ($item['variations'] as &$variation) {
                    $variation['stock'] = $stockMap[$variation['id']] ?? 0;
                    $variation['in_stock'] = $variation['stock'] > 0;
                }
                unset($variation);
            }
            unset($item);
        }
    }

    usort($items, fn($a, $b) => strcmp((string)$a['name'], (string)$b['name']));

    $result = ['products' => $items];
    square_cache_set('catalog_products', $result);

    return $result;
}

/**
 * Find product by slug or catalog id from normalized product list.
 */
function square_find_catalog_product(array $products, string $lookup): ?array {
    foreach ($products as $product) {
        if (($product['slug'] ?? '') === $lookup || ($product['id'] ?? '') === $lookup) {
            return $product;
        }
    }
    return null;
}
