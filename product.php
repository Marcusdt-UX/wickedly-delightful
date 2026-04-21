<?php
/**
 * Product Detail Page — Wickedly Delightful Scents
 * Route: /product.php?item={slug-or-catalog-id}
 */

require_once __DIR__ . '/api/square-catalog-data.php';

// Resolve the requested product
$lookup  = trim(strip_tags((string)($_GET['item'] ?? $_GET['id'] ?? '')));
$product = null;
$dataError = null;

if ($lookup === '') {
    header('Location: /shop.php');
    exit;
}

if (SQUARE_ACCESS_TOKEN) {
    $result = square_get_catalog_products(false);
    if (isset($result['error'])) {
        $dataError = $result['error'];
    } else {
        $product = square_find_catalog_product($result['products'] ?? [], $lookup);
    }
}

if ($product === null && $dataError === null) {
    http_response_code(404);
}

$checkoutComplete = isset($_GET['checkout']) && $_GET['checkout'] === 'complete';

// SEO / meta helpers
$metaTitle       = $product ? htmlspecialchars($product['name'] . ' — Wickedly Delightful Scents') : 'Product Not Found — Wickedly Delightful Scents';
$metaDesc        = $product ? htmlspecialchars(substr(strip_tags($product['description'] ?? ''), 0, 155)) : '';
$metaImage       = $product['image'] ?? '';

$productName     = $product['name'] ?? 'Product Not Found';
$productDesc     = $product['description'] ?? '';
$productImages   = $product['images'] ?? ($product['image'] ? [$product['image']] : []);
$productCategory = $product['category'] ?? '';
$variations      = $product['variations'] ?? [];
$firstVariation  = $variations[0] ?? null;
$priceMin        = $product['price_min'] ?? 0;
$priceMax        = $product['price_max'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $metaTitle; ?></title>
  <?php if ($metaDesc): ?>
  <meta name="description" content="<?php echo $metaDesc; ?>" />
  <?php endif; ?>
  <?php if ($metaImage): ?>
  <meta property="og:image" content="<?php echo htmlspecialchars($metaImage); ?>" />
  <meta property="og:type" content="product" />
  <meta property="og:title" content="<?php echo $metaTitle; ?>" />
  <?php if ($metaDesc): ?>
  <meta property="og:description" content="<?php echo $metaDesc; ?>" />
  <?php endif; ?>
  <?php endif; ?>
  <link rel="icon" type="image/png" href="/assets/logo.png" />
  <link rel="stylesheet" href="/css/styles.css" />
  <link rel="stylesheet" href="/css/shop.css" />
  <link rel="stylesheet" href="/css/product.css" />
</head>
<body>

  <!-- Grain texture overlay -->
  <div class="grain-overlay" aria-hidden="true">
    <svg width="100%" height="100%">
      <filter id="noise">
        <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="4" stitchTiles="stitch" />
        <feColorMatrix type="saturate" values="0" />
      </filter>
      <rect width="100%" height="100%" filter="url(#noise)" />
    </svg>
  </div>

  <!-- ========== NAVIGATION ========== -->
  <nav class="nav" id="nav">
    <div class="gradient-line"></div>
    <div class="nav-inner">
      <a href="/" class="nav-logo">
        <img src="/assets/logo.png" alt="Wickedly Delightful Scents" class="nav-logo-img" />
        <span class="nav-logo-text text-glow-red">Wickedly Delightful</span>
      </a>

      <div class="nav-links" id="nav-links">
        <a href="/" class="nav-link">Home</a>
        <a href="/shop.php" class="nav-link">Shop</a>
        <a href="/#contact" class="nav-link">Contact</a>
      </div>

      <button class="cart-toggle" id="cart-toggle" aria-label="Open cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span class="cart-count" id="cart-count">0</span>
      </button>

      <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="3" y1="6" x2="21" y2="6" /><line x1="3" y1="12" x2="21" y2="12" /><line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
    </div>

    <div class="nav-mobile" id="nav-mobile">
      <a href="/" class="nav-link">Home</a>
      <a href="/shop.php" class="nav-link">Shop</a>
      <a href="/#contact" class="nav-link">Contact</a>
    </div>
    <div class="gradient-line"></div>
  </nav>

  <!-- ========== BREADCRUMB ========== -->
  <nav class="product-breadcrumb" aria-label="Breadcrumb">
    <div class="section-inner">
      <ol class="breadcrumb-list">
        <li><a href="/">Home</a></li>
        <li aria-hidden="true">/</li>
        <li><a href="/shop.php">Shop</a></li>
        <li aria-hidden="true">/</li>
        <li aria-current="page"><?php echo htmlspecialchars($productName); ?></li>
      </ol>
    </div>
  </nav>

  <?php if ($checkoutComplete): ?>
  <div class="checkout-success" id="checkout-success">
    <div class="section-inner">
      <div class="checkout-success-inner ornate-card ornate-corners">
        <span class="corner-bl"></span><span class="corner-br"></span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:2.5rem;height:2.5rem;color:var(--accent);">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <div>
          <h3 class="font-serif">Thank You!</h3>
          <p>Your order has been placed. You'll receive a confirmation email shortly.</p>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ========== PRODUCT DETAIL ========== -->
  <main class="section product-detail-section">
    <div class="leather-texture" aria-hidden="true"></div>
    <div class="section-inner">

      <?php if ($dataError): ?>
      <div class="shop-notice ornate-card ornate-corners">
        <span class="corner-bl"></span><span class="corner-br"></span>
        <p>Unable to load product information right now. Please <a href="/shop.php">return to the shop</a>.</p>
      </div>

      <?php elseif ($product === null): ?>
      <div class="product-notfound ornate-card ornate-corners">
        <span class="corner-bl"></span><span class="corner-br"></span>
        <div class="about-accent-line"></div>
        <h2 class="font-serif">Product Not Found</h2>
        <p>We couldn't find that item. It may have been removed or the link may be incorrect.</p>
        <a href="/shop.php" class="btn-neumorphic product-back-btn">← Back to Shop</a>
      </div>

      <?php else: ?>
      <div class="product-layout">

        <!-- ---- Image Gallery ---- -->
        <div class="product-gallery">
          <?php if (!empty($productImages)): ?>
          <div class="product-image-main ornate-card ornate-corners">
            <span class="corner-bl"></span><span class="corner-br"></span>
            <img id="product-main-image"
                 src="<?php echo htmlspecialchars($productImages[0]); ?>"
                 alt="<?php echo htmlspecialchars($productName); ?>"
                 class="product-main-img" />
          </div>

          <?php if (count($productImages) > 1): ?>
          <div class="product-image-thumbs">
            <?php foreach ($productImages as $idx => $imgUrl): ?>
            <button class="product-thumb <?php echo $idx === 0 ? 'active' : ''; ?>"
                    data-img="<?php echo htmlspecialchars($imgUrl); ?>"
                    aria-label="View image <?php echo $idx + 1; ?>">
              <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                   alt="<?php echo htmlspecialchars($productName); ?> thumbnail <?php echo $idx + 1; ?>" />
            </button>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <?php else: ?>
          <div class="product-image-main product-image-placeholder ornate-card ornate-corners">
            <span class="corner-bl"></span><span class="corner-br"></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
              <line x1="7" y1="7" x2="7.01" y2="7"/>
            </svg>
          </div>
          <?php endif; ?>
        </div>

        <!-- ---- Product Info ---- -->
        <div class="product-info">
          <div class="about-accent-line"></div>

          <?php if ($productCategory): ?>
          <p class="product-category"><?php echo htmlspecialchars($productCategory); ?></p>
          <?php endif; ?>

          <h1 class="product-name font-serif text-glow-red tracking-luxury"><?php echo htmlspecialchars($productName); ?></h1>

          <!-- Price display -->
          <div class="product-price-row">
            <?php if ($priceMin === $priceMax): ?>
            <span class="product-price" id="product-price">
              $<?php echo number_format($priceMin / 100, 2); ?>
            </span>
            <?php elseif ($priceMin > 0): ?>
            <span class="product-price" id="product-price">
              $<?php echo number_format($priceMin / 100, 2); ?> – $<?php echo number_format($priceMax / 100, 2); ?>
            </span>
            <?php else: ?>
            <span class="product-price product-price--free" id="product-price">Free</span>
            <?php endif; ?>

            <?php
            $anyInStock = false;
            foreach ($variations as $v) {
                if ($v['in_stock'] ?? false) { $anyInStock = true; break; }
            }
            ?>
            <span class="shop-card-stock <?php echo $anyInStock ? 'in' : 'out'; ?>" id="product-stock-badge">
              <?php echo $anyInStock ? 'In Stock' : 'Out of Stock'; ?>
            </span>
          </div>

          <?php if (count($variations) > 1): ?>
          <!-- Variation selector -->
          <div class="product-variations">
            <p class="product-label">Choose Option</p>
            <select class="product-variation-select input-neumorphic"
                    id="product-variation-select">
              <?php foreach ($variations as $v): ?>
              <option value="<?php echo htmlspecialchars($v['id']); ?>"
                      data-price="<?php echo intval($v['price']); ?>"
                      data-in-stock="<?php echo ($v['in_stock'] ?? false) ? '1' : '0'; ?>"
                      data-stock="<?php echo intval($v['stock'] ?? 0); ?>"
                      <?php echo !($v['in_stock'] ?? false) ? 'disabled' : ''; ?>>
                <?php echo htmlspecialchars($v['name']); ?>
                — $<?php echo number_format($v['price'] / 100, 2); ?>
                <?php echo !($v['in_stock'] ?? false) ? '(Out of Stock)' : ''; ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>

          <!-- Qty + Add to Cart -->
          <?php if ($firstVariation): ?>
          <div class="product-actions">
            <div class="qty-control" id="product-qty-control">
              <button class="qty-btn btn-neumorphic" id="product-qty-dec" data-action="decrease" aria-label="Decrease quantity">−</button>
              <span class="qty-value" id="product-qty-value">1</span>
              <button class="qty-btn btn-neumorphic" id="product-qty-inc" data-action="increase" aria-label="Increase quantity">+</button>
            </div>

            <button class="add-to-cart btn-neumorphic product-add-to-cart"
                    id="product-add-to-cart"
                    data-variation-id="<?php echo htmlspecialchars($firstVariation['id']); ?>"
                    data-product-name="<?php echo htmlspecialchars($productName); ?>"
                    data-price="<?php echo intval($firstVariation['price']); ?>"
                    data-stock="<?php echo intval($firstVariation['stock'] ?? 0); ?>"
                    <?php echo !($firstVariation['in_stock'] ?? false) ? 'disabled' : ''; ?>>
              <?php echo ($firstVariation['in_stock'] ?? false) ? 'Add to Cart' : 'Sold Out'; ?>
            </button>
          </div>

          <p class="product-shipping-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:1rem;height:1rem;display:inline;vertical-align:middle;margin-right:0.3rem;">
              <rect x="1" y="3" width="15" height="13" rx="1"/>
              <path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
            Free local pickup available. Shipping calculated at checkout.
          </p>
          <?php endif; ?>

          <!-- Description -->
          <?php if ($productDesc): ?>
          <div class="product-description">
            <div class="gradient-line-subtle" style="margin:1.5rem 0 1.25rem;"></div>
            <h3 class="product-desc-heading">About this product</h3>
            <div class="product-desc-body">
              <?php echo nl2br(htmlspecialchars(strip_tags($productDesc))); ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- SKU / metadata -->
          <?php
          $firstSku = '';
          foreach ($variations as $v) {
              if (!empty($v['sku'])) { $firstSku = $v['sku']; break; }
          }
          ?>
          <?php if ($firstSku || $productCategory): ?>
          <div class="product-meta-row">
            <?php if ($productCategory): ?>
            <span class="product-meta-item">Category: <strong><?php echo htmlspecialchars($productCategory); ?></strong></span>
            <?php endif; ?>
            <?php if ($firstSku): ?>
            <span class="product-meta-item">SKU: <strong><?php echo htmlspecialchars($firstSku); ?></strong></span>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <a href="/shop.php" class="product-back-link">← Back to Shop</a>
        </div>

      </div><!-- /.product-layout -->
      <?php endif; ?>

    </div>
  </main>

  <!-- Lace edge -->
  <div class="lace-edge lace-edge--flip" aria-hidden="true">
    <svg viewBox="0 0 1200 40" preserveAspectRatio="none" fill="none">
      <defs>
        <pattern id="lace-pdp" x="0" y="0" width="60" height="40" patternUnits="userSpaceOnUse">
          <path d="M0 20 Q15 5, 30 20 Q45 35, 60 20" stroke="currentColor" stroke-width="1.2" fill="none" opacity="0.5"/>
          <circle cx="30" cy="20" r="2" fill="currentColor" opacity="0.3"/>
          <circle cx="15" cy="12" r="1.2" fill="currentColor" opacity="0.2"/>
          <circle cx="45" cy="12" r="1.2" fill="currentColor" opacity="0.2"/>
        </pattern>
      </defs>
      <rect width="1200" height="40" fill="url(#lace-pdp)"/>
    </svg>
  </div>

  <!-- ========== FOOTER ========== -->
  <footer class="footer" id="footer">
    <div class="gradient-line"></div>
    <div class="footer-inner">
      <div class="footer-grid">
        <div class="footer-brand">
          <h3 class="text-glow-red font-serif">Wickedly Delightful</h3>
          <p>Handcrafted wax melts made with premium ingredients. Each scent is carefully curated to bring warmth and comfort to your home.</p>
        </div>
        <div class="footer-links">
          <h4>Quick Links</h4>
          <a href="/" class="hover-glow">Home</a>
          <a href="/shop.php" class="hover-glow">Shop</a>
          <a href="/#contact" class="hover-glow">Contact</a>
        </div>
        <div class="footer-social">
          <h4>Follow Us</h4>
          <div class="social-icons">
            <a href="#" class="social-icon hover-glow embossed" aria-label="Instagram">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
              </svg>
            </a>
            <a href="#" class="social-icon hover-glow embossed" aria-label="Facebook">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
              </svg>
            </a>
            <a href="#" class="social-icon hover-glow embossed" aria-label="Email">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
      <div class="gradient-line-subtle"></div>
      <div class="footer-bottom">
        <p>Made with <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg> by Wickedly Delightful Scents &copy; 2026</p>
      </div>
    </div>
    <div class="gradient-line" style="margin-top:1.5rem"></div>
  </footer>

  <div style="text-align:center;padding:0.75rem 1rem;background:#0a0a0a;border-top:1px solid rgba(255,255,255,0.06);font-size:0.75rem;color:rgba(255,255,255,0.35);letter-spacing:0.03em;">
    Website designed by <a href="https://thomaspublishinghouse.com" target="_blank" rel="noopener noreferrer" style="color:rgba(255,255,255,0.5);text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.15);transition:color 0.2s,border-color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.8)';this.style.borderColor='rgba(255,255,255,0.4)'" onmouseout="this.style.color='rgba(255,255,255,0.5)';this.style.borderColor='rgba(255,255,255,0.15)'">Thomas Publishing House</a>
  </div>

  <!-- ========== CART DRAWER (shared) ========== -->
  <div class="cart-overlay" id="cart-overlay"></div>
  <aside class="cart-drawer" id="cart-drawer">
    <div class="cart-drawer-header">
      <h3 class="font-serif text-glow-red tracking-luxury">Your Cart</h3>
      <button class="cart-close" id="cart-close" aria-label="Close cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="gradient-line"></div>
    <div class="cart-items" id="cart-items">
      <p class="cart-empty" id="cart-empty">Your cart is empty</p>
    </div>
    <div class="cart-footer" id="cart-footer" style="display:none;">
      <div class="gradient-line"></div>
      <div class="cart-shipping">
        <label for="cart-shipping-method" class="cart-shipping-label">Shipping Method</label>
        <select id="cart-shipping-method" class="cart-shipping-select input-neumorphic">
          <option value="standard" data-amount="895">Standard Shipping (3-5 business days) — $8.95</option>
          <option value="express" data-amount="1595">Express Shipping (1-2 business days) — $15.95</option>
          <option value="pickup" data-amount="0">Local Pickup — Free</option>
        </select>
      </div>
      <div class="cart-total cart-subtotal">
        <span class="font-serif tracking-luxury">Subtotal</span>
        <span class="cart-total-amount" id="cart-subtotal">$0.00</span>
      </div>
      <div class="cart-total cart-shipping-row">
        <span class="font-serif tracking-luxury">Shipping</span>
        <span class="cart-total-amount" id="cart-shipping-amount">$0.00</span>
      </div>
      <div class="cart-total">
        <span class="font-serif tracking-luxury">Total</span>
        <span class="cart-total-amount text-glow-red" id="cart-total">$0.00</span>
      </div>
      <button class="checkout-btn btn-neumorphic" id="checkout-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1.2rem;height:1.2rem;">
          <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
        </svg>
        Proceed to Checkout
      </button>
      <p class="cart-shipping-note">Shipping address is collected securely on Square checkout.</p>
    </div>
  </aside>

  <script src="/js/main.js"></script>
  <script src="/js/shop.js"></script>
  <script src="/js/product.js?v=<?php echo filemtime(__DIR__ . '/js/product.js'); ?>"></script>
</body>
</html>
