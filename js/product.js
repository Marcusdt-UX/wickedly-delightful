/**
 * Product Detail Page — JS
 * Manages variation selection, qty control, stock display, and Add to Cart
 * on the product detail page. Cart state is handled by shop.js (already loaded).
 */
(function () {
  'use strict';

  const CART_KEY = 'wds_cart';

  // ---- Elements ----
  const addBtn       = document.getElementById('product-add-to-cart');
  const qtyValue     = document.getElementById('product-qty-value');
  const qtyDec       = document.getElementById('product-qty-dec');
  const qtyInc       = document.getElementById('product-qty-inc');
  const varSelect    = document.getElementById('product-variation-select');
  const priceEl      = document.getElementById('product-price');
  const stockBadge   = document.getElementById('product-stock-badge');
  const mainImage    = document.getElementById('product-main-image');
  const thumbBtns    = document.querySelectorAll('.product-thumb');

  if (!addBtn) return; // product not found page, nothing to wire

  // ---- Helpers shared with shop.js (already loaded) ----
  function readCart() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch {
      return [];
    }
  }

  function getCartQtyForVariation(variationId) {
    const existing = readCart().find(i => i.variationId === variationId);
    return existing ? existing.qty : 0;
  }

  function getCurrentLimit() {
    return parseInt(addBtn.dataset.stock || '0', 10);
  }

  function normalizeCurrentVariationCart() {
    const variationId = getCurrentVariationId();
    const limit = getCurrentLimit();
    const cart = readCart();
    let changed = false;

    const normalized = cart.flatMap((item) => {
      if (item.variationId !== variationId) {
        return [item];
      }

      const qty = Math.max(0, Math.min(item.qty, limit));
      if (qty !== item.qty) {
        changed = true;
      }

      return qty > 0 ? [{ ...item, qty }] : [];
    });

    if (changed) {
      localStorage.setItem(CART_KEY, JSON.stringify(normalized));
      window.dispatchEvent(new StorageEvent('storage', { key: CART_KEY }));
    }

    return normalized;
  }

  function getCurrentVariationId() {
    if (varSelect) {
      const opt = varSelect.options[varSelect.selectedIndex];
      return opt ? opt.value : addBtn.dataset.variationId;
    }
    return addBtn.dataset.variationId;
  }

  function getCurrentStock() {
    return getCurrentLimit();
  }

  function syncControls() {
    normalizeCurrentVariationCart();

    const variationId = getCurrentVariationId();
    const stock = getCurrentStock();
    const inCart = getCartQtyForVariation(variationId);
    const remaining = Math.max(0, stock - inCart);

    let qty = parseInt(qtyValue.textContent || '1', 10);
    qty = Math.max(1, qty);
    if (remaining > 0 && qty > remaining) qty = remaining;
    if (remaining === 0) qty = 1;
    qtyValue.textContent = qty;

    if (qtyDec) qtyDec.disabled = qty <= 1;
    if (qtyInc) qtyInc.disabled = remaining <= 0 || qty >= remaining;

    const disabled = remaining <= 0;
    addBtn.disabled = disabled;
    addBtn.textContent = stock === 0 ? 'Sold Out' : disabled ? 'Max in Cart' : 'Add to Cart';

    if (stockBadge) {
      if (stock === 0) {
        stockBadge.textContent = 'Out of Stock';
        stockBadge.className = 'shop-card-stock out';
      } else {
        stockBadge.textContent = 'In Stock';
        stockBadge.className = 'shop-card-stock in';
      }
    }
  }

  // ---- Variation change ----
  if (varSelect) {
    varSelect.addEventListener('change', () => {
      const opt = varSelect.options[varSelect.selectedIndex];
      if (!opt) return;

      const price = parseInt(opt.dataset.price || '0', 10);
      const stock = parseInt(opt.dataset.stock || '0', 10);

      addBtn.dataset.variationId = opt.value;
      addBtn.dataset.price = price;
      addBtn.dataset.stock = stock;

      if (priceEl) {
        priceEl.textContent = '$' + (price / 100).toFixed(2);
      }

      qtyValue.textContent = '1';
      syncControls();
    });
  }

  // ---- Qty buttons ----
  if (qtyDec) {
    qtyDec.addEventListener('click', () => {
      let val = parseInt(qtyValue.textContent, 10);
      if (val > 1) val--;
      qtyValue.textContent = val;
      syncControls();
    });
  }

  if (qtyInc) {
    qtyInc.addEventListener('click', () => {
      const variationId = getCurrentVariationId();
      const stock = getCurrentStock();
      const inCart = getCartQtyForVariation(variationId);
      const remaining = Math.max(0, stock - inCart);
      let val = parseInt(qtyValue.textContent, 10);
      if (val < remaining) val++;
      qtyValue.textContent = val;
      syncControls();
    });
  }

  // ---- Add to Cart ----
  addBtn.addEventListener('click', () => {
    const variationId = getCurrentVariationId();
    const stock       = getCurrentStock();
    const price       = parseInt(addBtn.dataset.price || '0', 10);
    const productName = addBtn.dataset.productName || '';
    const requestedQty = parseInt(qtyValue.textContent || '1', 10);

    const inCart    = getCartQtyForVariation(variationId);
    const remaining = Math.max(0, stock - inCart);

    if (remaining <= 0) {
      syncControls();
      return;
    }

    const qty = Math.min(requestedQty, remaining);

    // Add to cart via shop.js global (it exposes nothing, so read/write localStorage directly)
    try {
      const cart = readCart();
      const existing = cart.find(i => i.variationId === variationId);
      let cartName = productName;
      if (varSelect) {
        const opt = varSelect.options[varSelect.selectedIndex];
        if (opt && opt.textContent.trim() !== 'Regular') {
          cartName += ' — ' + opt.textContent.split('—')[0].trim();
        }
      }
      if (existing) {
        existing.qty += qty;
      } else {
        cart.push({ variationId, name: cartName, price, qty });
      }
      localStorage.setItem(CART_KEY, JSON.stringify(cart));
    } catch {
      return;
    }

    // Trigger shop.js renderCart by dispatching storage event on same window
    window.dispatchEvent(new StorageEvent('storage', { key: CART_KEY }));

    // Open cart drawer
    const cartDrawer  = document.getElementById('cart-drawer');
    const cartOverlay = document.getElementById('cart-overlay');
    if (cartDrawer) cartDrawer.classList.add('open');
    if (cartOverlay) cartOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Feedback
    const origText = addBtn.textContent;
    addBtn.textContent = 'Added ✓';
    addBtn.classList.add('added');
    setTimeout(() => {
      addBtn.textContent = origText;
      addBtn.classList.remove('added');
      syncControls();
    }, 1200);

    syncControls();
  });

  // ---- Image thumbs ----
  thumbBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      thumbBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if (mainImage) {
        mainImage.src = btn.dataset.img;
      }
    });
  });

  // ---- Re-sync when cart changes from another tab ----
  window.addEventListener('storage', (e) => {
    if (e.key === 'wds_cart') syncControls();
  });

  // ---- Initial sync ----
  syncControls();
})();
