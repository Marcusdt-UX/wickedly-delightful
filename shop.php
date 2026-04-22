<?php
/**
 * Shop page — Wickedly Delightful Scents
 * Pulls live catalog from Square API, renders with matching gothic theme.
 */

// Pre-fetch products server-side for SEO / initial render
$products = [];
$apiError = null;

$configPath = __DIR__ . '/api/square-catalog-data.php';
if (file_exists($configPath)) {
    require_once $configPath;
    if (defined('SQUARE_ACCESS_TOKEN') && SQUARE_ACCESS_TOKEN) {
    $catalogResult = square_get_catalog_products(false);
    if (!isset($catalogResult['error'])) {
      $products = $catalogResult['products'] ?? [];
    } else {
      $apiError = $catalogResult['error'];
        }
    }
}

$checkoutComplete = isset($_GET['checkout']) && $_GET['checkout'] === 'complete';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shop — Wickedly Delightful Scents</title>
  <meta name="description" content="Shop our handcrafted wax melts. Browse fragrances, check availability, and order directly." />
  <link rel="icon" type="image/png" href="assets/logo.png" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/shop.css" />
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
        <img src="assets/logo.png" alt="Wickedly Delightful Scents" class="nav-logo-img" />
        <span class="nav-logo-text text-glow-red">Wickedly Delightful</span>
      </a>

      <div class="nav-links" id="nav-links">
        <a href="/" class="nav-link">Home</a>
        <a href="shop.php" class="nav-link active">Shop</a>
        <a href="/#contact" class="nav-link">Contact</a>
      </div>

      <!-- Cart icon -->
      <button class="cart-toggle" id="cart-toggle" aria-label="Open cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span class="cart-count" id="cart-count">0</span>
      </button>

      <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
    </div>

    <div class="nav-mobile" id="nav-mobile">
      <a href="/" class="nav-link">Home</a>
      <a href="shop.php" class="nav-link active">Shop</a>
      <a href="/#contact" class="nav-link">Contact</a>
    </div>
    <div class="gradient-line"></div>
  </nav>

  <!-- ========== SHOP HERO ========== -->
  <section class="shop-hero">
    <div class="leather-texture" aria-hidden="true"></div>
    <div class="section-inner" style="padding-top:2rem;padding-bottom:1.5rem;">
      <div class="section-header">
        <svg class="filigree" viewBox="0 0 400 100" fill="none" aria-hidden="true">
          <defs><linearGradient id="fgShop" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="currentColor" stop-opacity="0"/><stop offset="50%" stop-color="currentColor" stop-opacity="1"/><stop offset="100%" stop-color="currentColor" stop-opacity="0"/></linearGradient></defs>
          <path d="M 200 50 Q 180 30, 160 50 Q 180 70, 200 50 Q 220 30, 240 50 Q 220 70, 200 50" stroke="url(#fgShop)" stroke-width="1.5" fill="none"/>
          <path d="M 160 50 Q 140 45, 120 50 Q 100 55, 80 50 Q 60 45, 40 50 Q 20 55, 0 50" stroke="url(#fgShop)" stroke-width="1.2" fill="none"/>
          <path d="M 240 50 Q 260 45, 280 50 Q 300 55, 320 50 Q 340 45, 360 50 Q 380 55, 400 50" stroke="url(#fgShop)" stroke-width="1.2" fill="none"/>
          <circle cx="200" cy="50" r="4" fill="currentColor" opacity="0.8"/><circle cx="160" cy="50" r="3" fill="currentColor" opacity="0.6"/><circle cx="240" cy="50" r="3" fill="currentColor" opacity="0.6"/>
        </svg>
        <h1 class="section-title debossed-text tracking-luxury">
          Our <span class="accent text-glow-red">Shop</span>
        </h1>
        <p class="section-subtitle">Handcrafted wax melts — browse, choose, and order</p>
      </div>
    </div>
  </section>

  <!-- Lace edge -->
  <div class="lace-edge" aria-hidden="true">
    <svg viewBox="0 0 1200 40" preserveAspectRatio="none" fill="none">
      <defs>
        <pattern id="lace-shop" x="0" y="0" width="60" height="40" patternUnits="userSpaceOnUse">
          <path d="M0 20 Q15 5, 30 20 Q45 35, 60 20" stroke="currentColor" stroke-width="1.2" fill="none" opacity="0.5"/>
          <circle cx="30" cy="20" r="2" fill="currentColor" opacity="0.3"/>
          <circle cx="15" cy="12" r="1.2" fill="currentColor" opacity="0.2"/>
          <circle cx="45" cy="12" r="1.2" fill="currentColor" opacity="0.2"/>
        </pattern>
      </defs>
      <rect width="1200" height="40" fill="url(#lace-shop)"/>
    </svg>
  </div>

  <!-- ========== CHECKOUT SUCCESS BANNER ========== -->
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

  <!-- ========== PRODUCTS GRID ========== -->
  <section class="section shop-products" id="shop-products">
    <div class="leather-texture" aria-hidden="true"></div>
    <div class="section-inner">

      <?php if ($apiError): ?>
      <div class="shop-notice ornate-card ornate-corners">
        <span class="corner-bl"></span><span class="corner-br"></span>
        <p>Unable to load products right now. Please try again later.</p>
      </div>
      <?php endif; ?>

      <div class="shop-grid" id="shop-grid">
        <?php if (empty($products) && !$apiError): ?>
        <div class="shop-notice ornate-card ornate-corners">
          <span class="corner-bl"></span><span class="corner-br"></span>
          <p>No products are available right now. Please check back soon.</p>
        </div>
        <?php else: ?>
          <?php foreach ($products as $i => $product): ?>
          <div class="shop-card ornate-card ornate-corners reveal <?php echo $i > 0 ? 'reveal-delay-' . min($i, 4) : ''; ?>"
               data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
            <span class="corner-bl"></span><span class="corner-br"></span>

            <div class="shop-card-image">
              <?php if ($product['image']): ?>
              <img src="<?php echo htmlspecialchars($product['image']); ?>"
                   alt="<?php echo htmlspecialchars($product['name']); ?>"
                   loading="lazy" />
              <?php else: ?>
              <div class="shop-card-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
              </div>
              <?php endif; ?>
              <div class="product-image-overlay"></div>
            </div>

            <div class="shop-card-body">
              <div class="about-accent-line"></div>
              <h3 class="shop-card-title">
                <a href="<?php echo htmlspecialchars($product['product_url'] ?? ('/product.php?item=' . rawurlencode($product['id'] ?? ''))); ?>" class="shop-card-title-link">
                  <?php echo htmlspecialchars($product['name']); ?>
                </a>
              </h3>
              <?php if ($product['description']): ?>
              <p class="shop-card-desc"><?php echo htmlspecialchars(strip_tags($product['description'])); ?></p>
              <?php endif; ?>

              <?php if (!empty($product['category'])): ?>
              <p class="shop-card-category"><?php echo htmlspecialchars($product['category']); ?></p>
              <?php endif; ?>

              <?php
              $variation = $product['variations'][0] ?? null;
              if ($variation):
                $dollars = number_format($variation['price'] / 100, 2);
                $inStock = $variation['in_stock'] ?? true;
              ?>
              <div class="shop-card-price-row">
                <span class="shop-card-price text-glow-red">$<?php echo $dollars; ?></span>
                <?php if (!$inStock): ?>
                <span class="shop-card-stock out">Out of Stock</span>
                <?php else: ?>
                <span class="shop-card-stock in">In Stock</span>
                <?php endif; ?>
              </div>

              <?php if (count($product['variations']) > 1): ?>
              <select class="shop-variation input-neumorphic"
                      data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                <?php foreach ($product['variations'] as $v): ?>
                <option value="<?php echo htmlspecialchars($v['id']); ?>"
                        data-price="<?php echo $v['price']; ?>"
                        data-in-stock="<?php echo ($v['in_stock'] ?? true) ? '1' : '0'; ?>"
                        data-stock="<?php echo intval($v['stock'] ?? 0); ?>">
                  <?php echo htmlspecialchars($v['name']); ?> — $<?php echo number_format($v['price'] / 100, 2); ?>
                </option>
                <?php endforeach; ?>
              </select>
              <?php endif; ?>

              <div class="shop-card-actions">
                <div class="qty-control">
                  <button class="qty-btn btn-neumorphic" data-action="decrease" aria-label="Decrease quantity">−</button>
                  <span class="qty-value">1</span>
                  <button class="qty-btn btn-neumorphic" data-action="increase" aria-label="Increase quantity">+</button>
                </div>
                <button class="add-to-cart btn-neumorphic"
                        data-variation-id="<?php echo htmlspecialchars($variation['id']); ?>"
                        data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                        data-price="<?php echo $variation['price']; ?>"
                        data-stock="<?php echo intval($variation['stock'] ?? 0); ?>"
                        <?php echo !$inStock ? 'disabled' : ''; ?>>
                  <?php echo $inStock ? 'Add to Cart' : 'Sold Out'; ?>
                </button>
              </div>
              <a class="shop-details-link" href="<?php echo htmlspecialchars($product['product_url'] ?? ('/product.php?item=' . rawurlencode($product['id'] ?? ''))); ?>">View Item Details</a>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <!-- Lace edge bottom -->
  <div class="lace-edge lace-edge--flip" aria-hidden="true">
    <svg viewBox="0 0 1200 40" preserveAspectRatio="none" fill="none">
      <defs>
        <pattern id="lace-shop2" x="0" y="0" width="60" height="40" patternUnits="userSpaceOnUse">
          <path d="M0 20 Q15 5, 30 20 Q45 35, 60 20" stroke="currentColor" stroke-width="1.2" fill="none" opacity="0.5"/>
          <circle cx="30" cy="20" r="2" fill="currentColor" opacity="0.3"/>
          <circle cx="15" cy="12" r="1.2" fill="currentColor" opacity="0.2"/>
          <circle cx="45" cy="12" r="1.2" fill="currentColor" opacity="0.2"/>
        </pattern>
      </defs>
      <rect width="1200" height="40" fill="url(#lace-shop2)"/>
    </svg>
  </div>

  <!-- ========== CART DRAWER ========== -->
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

  <!-- ========== FOOTER ========== -->
  <footer class="footer" id="footer">
    <div class="gradient-line"></div>
    <div class="footer-inner">

      <div style="margin-bottom:2rem;">
        <svg class="filigree filigree--sm" viewBox="0 0 400 100" fill="none" aria-hidden="true">
          <defs><linearGradient id="fgF" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="currentColor" stop-opacity="0"/><stop offset="50%" stop-color="currentColor" stop-opacity="1"/><stop offset="100%" stop-color="currentColor" stop-opacity="0"/></linearGradient></defs>
          <path d="M 200 50 Q 180 30, 160 50 Q 180 70, 200 50 Q 220 30, 240 50 Q 220 70, 200 50" stroke="url(#fgF)" stroke-width="1.5" fill="none"/>
          <path d="M 160 50 Q 140 45, 120 50 Q 100 55, 80 50 Q 60 45, 40 50 Q 20 55, 0 50" stroke="url(#fgF)" stroke-width="1.2" fill="none"/>
          <path d="M 240 50 Q 260 45, 280 50 Q 300 55, 320 50 Q 340 45, 360 50 Q 380 55, 400 50" stroke="url(#fgF)" stroke-width="1.2" fill="none"/>
          <circle cx="200" cy="50" r="4" fill="currentColor" opacity="0.8"/><circle cx="160" cy="50" r="3" fill="currentColor" opacity="0.6"/><circle cx="240" cy="50" r="3" fill="currentColor" opacity="0.6"/>
        </svg>
      </div>

      <div class="footer-grid">
        <div class="footer-brand">
          <h3 class="text-glow-red font-serif">Wickedly Delightful</h3>
          <p>Handcrafted wax melts made with premium ingredients. Each scent is carefully curated to bring warmth and comfort to your home.</p>
        </div>

        <div class="footer-links">
          <h4>Quick Links</h4>
          <a href="/" class="hover-glow">Home</a>
          <a href="shop.php" class="hover-glow">Shop</a>
          <a href="/#contact" class="hover-glow">Contact</a>
        </div>

        <div class="footer-social">
          <h4>Need Help?</h4>
          <p>Questions about an order, pickup, or scents? Use the contact form on the homepage.</p>
          <a href="/#contact" class="footer-contact-link hover-glow">Contact Wickedly Delightful</a>
        </div>
      </div>

      <div class="gradient-line-subtle"></div>
      <div class="footer-bottom">
        <p>
          Made with
          <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
          by Wickedly Delightful Scents &copy; 2026
        </p>
      </div>

    </div>
    <div class="gradient-line" style="margin-top:1.5rem"></div>
  </footer>

  <div style="text-align:center;padding:0.75rem 1rem;background:#0a0a0a;border-top:1px solid rgba(255,255,255,0.06);font-size:0.75rem;color:rgba(255,255,255,0.35);letter-spacing:0.03em;">
    Website designed by <a href="https://thomaspublishinghouse.com" target="_blank" rel="noopener noreferrer" style="color:rgba(255,255,255,0.5);text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.15);transition:color 0.2s,border-color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.8)';this.style.borderColor='rgba(255,255,255,0.4)'" onmouseout="this.style.color='rgba(255,255,255,0.5)';this.style.borderColor='rgba(255,255,255,0.15)'">Thomas Publishing House</a>
  </div>

  <script src="js/main.js?v=<?php echo filemtime(__DIR__ . '/js/main.js'); ?>"></script>
  <script src="js/shop.js?v=<?php echo filemtime(__DIR__ . '/js/shop.js'); ?>"></script>
</body>
</html>
