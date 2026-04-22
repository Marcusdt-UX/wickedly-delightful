/**
 * Shop page — Cart & checkout logic
 * Wickedly Delightful Scents
 */
(function () {
  'use strict';

  // ---- Cart State (persisted in localStorage) ----
  const CART_KEY = 'wds_cart';

  function readCart() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch {
      return [];
    }
  }

  function getInventoryLimitForVariation(variationId) {
    if (!variationId) return null;

    const matchingOption = document.querySelector('.shop-variation option[value="' + CSS.escape(variationId) + '"]');
    if (matchingOption) {
      return parseInt(matchingOption.dataset.stock || '0', 10);
    }

    const matchingButton = document.querySelector('.add-to-cart[data-variation-id="' + CSS.escape(variationId) + '"]');
    if (matchingButton) {
      return parseInt(matchingButton.dataset.stock || '0', 10);
    }

    return null;
  }

  function normalizeCart(cart) {
    let changed = false;
    const normalized = [];

    cart.forEach((item) => {
      const limit = getInventoryLimitForVariation(item.variationId);
      if (limit === null) {
        normalized.push(item);
        return;
      }

      const qty = Math.max(0, Math.min(item.qty, limit));
      if (qty !== item.qty) {
        changed = true;
      }

      if (qty > 0) {
        normalized.push({ ...item, qty });
      } else {
        changed = true;
      }
    });

    return { cart: normalized, changed };
  }

  function getCart() {
    const cart = readCart();
    const normalized = normalizeCart(cart);

    if (normalized.changed) {
      saveCart(normalized.cart);
    }

    return normalized.cart;
  }

  function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  }

  function getCartQtyForVariation(variationId) {
    const existing = getCart().find(i => i.variationId === variationId);
    return existing ? existing.qty : 0;
  }

  function addToCart(variationId, name, priceCents, qty) {
    const cart = getCart();
    const existing = cart.find(i => i.variationId === variationId);
    const limit = getInventoryLimitForVariation(variationId);
    const currentQty = existing ? existing.qty : 0;
    const maxAddable = limit === null ? qty : Math.max(0, limit - currentQty);
    const safeQty = Math.max(0, Math.min(qty, maxAddable));

    if (safeQty <= 0) {
      renderCart();
      return false;
    }

    if (existing) {
      existing.qty += safeQty;
    } else {
      cart.push({ variationId, name, price: priceCents, qty: safeQty });
    }
    saveCart(cart);
    renderCart();
    return true;
  }

  function removeFromCart(variationId) {
    const cart = getCart().filter(i => i.variationId !== variationId);
    saveCart(cart);
    renderCart();
  }

  function clearCart() {
    localStorage.removeItem(CART_KEY);
    renderCart();
  }

  // ---- Cart UI ----
  const cartToggle  = document.getElementById('cart-toggle');
  const cartDrawer  = document.getElementById('cart-drawer');
  const cartOverlay = document.getElementById('cart-overlay');
  const cartClose   = document.getElementById('cart-close');
  const cartItems   = document.getElementById('cart-items');
  const cartEmpty   = document.getElementById('cart-empty');
  const cartFooter  = document.getElementById('cart-footer');
  const cartTotal   = document.getElementById('cart-total');
  const cartSubtotal = document.getElementById('cart-subtotal');
  const cartShippingAmount = document.getElementById('cart-shipping-amount');
  const cartCount   = document.getElementById('cart-count');
  const shippingSelect = document.getElementById('cart-shipping-method');
  const checkoutBtn = document.getElementById('checkout-btn');

  function getShippingAmountCents() {
    if (!shippingSelect) return 0;
    const selected = shippingSelect.options[shippingSelect.selectedIndex];
    return parseInt(selected?.dataset.amount || '0', 10);
  }

  function openCart() {
    cartDrawer.classList.add('open');
    cartOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeCart() {
    cartDrawer.classList.remove('open');
    cartOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (cartToggle) cartToggle.addEventListener('click', openCart);
  if (cartClose) cartClose.addEventListener('click', closeCart);
  if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

  function renderCart() {
    const cart = getCart();
    const totalItems = cart.reduce((s, i) => s + i.qty, 0);
    const subtotalCents = cart.reduce((s, i) => s + (i.price * i.qty), 0);
    const shippingCents = cart.length > 0 ? getShippingAmountCents() : 0;
    const totalCents = subtotalCents + shippingCents;

    // Badge
    if (cartCount) {
      cartCount.textContent = totalItems;
      cartCount.classList.toggle('visible', totalItems > 0);
    }

    // Items
    if (cartItems) {
      // Remove old cart-item elements but keep the empty msg
      cartItems.querySelectorAll('.cart-item').forEach(el => el.remove());

      if (cart.length === 0) {
        if (cartEmpty) cartEmpty.style.display = '';
        if (cartFooter) cartFooter.style.display = 'none';
      } else {
        if (cartEmpty) cartEmpty.style.display = 'none';
        if (cartFooter) cartFooter.style.display = '';

        cart.forEach(item => {
          const el = document.createElement('div');
          el.className = 'cart-item';
          el.innerHTML =
            '<div class="cart-item-info">' +
              '<div class="cart-item-name">' + escapeHtml(item.name) + '</div>' +
              '<div class="cart-item-meta">Qty: ' + item.qty + '</div>' +
            '</div>' +
            '<div class="cart-item-price">$' + (item.price * item.qty / 100).toFixed(2) + '</div>' +
            '<button class="cart-item-remove" data-vid="' + escapeHtml(item.variationId) + '" aria-label="Remove">' +
              '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
            '</button>';
          cartItems.appendChild(el);
        });

        // Remove buttons
        cartItems.querySelectorAll('.cart-item-remove').forEach(btn => {
          btn.addEventListener('click', () => {
            removeFromCart(btn.dataset.vid);
          });
        });
      }
    }

    // Total
    if (cartSubtotal) cartSubtotal.textContent = '$' + (subtotalCents / 100).toFixed(2);
    if (cartShippingAmount) cartShippingAmount.textContent = '$' + (shippingCents / 100).toFixed(2);
    if (cartTotal) cartTotal.textContent = '$' + (totalCents / 100).toFixed(2);

    // Keep product card controls aligned with current cart quantities.
    document.querySelectorAll('.shop-card').forEach(card => syncQtyControl(card));
  }

  function getSelectedVariationStock(card) {
    const select = card.querySelector('.shop-variation');
    if (select) {
      const opt = select.options[select.selectedIndex];
      return parseInt(opt?.dataset.stock || '0', 10);
    }

    const addBtn = card.querySelector('.add-to-cart');
    return parseInt(addBtn?.dataset.stock || '0', 10);
  }

  function syncQtyControl(card) {
    const qtyEl = card.querySelector('.qty-value');
    const minusBtn = card.querySelector('.qty-btn[data-action="decrease"]');
    const plusBtn = card.querySelector('.qty-btn[data-action="increase"]');
    const addBtn = card.querySelector('.add-to-cart');

    if (!qtyEl || !addBtn) return;

    const selectedVariationId = addBtn.dataset.variationId;
    const stock = getSelectedVariationStock(card);
    const alreadyInCart = selectedVariationId ? getCartQtyForVariation(selectedVariationId) : 0;
    const remaining = Math.max(0, stock - alreadyInCart);

    let qty = parseInt(qtyEl.textContent || '1', 10);
    qty = Math.max(1, qty);
    if (remaining > 0 && qty > remaining) {
      qty = remaining;
    }
    if (remaining === 0) {
      qty = 1;
    }
    qtyEl.textContent = qty;

    if (minusBtn) minusBtn.disabled = qty <= 1;
    if (plusBtn) plusBtn.disabled = remaining <= 0 || qty >= remaining;

    if (remaining <= 0) {
      addBtn.disabled = true;
      addBtn.textContent = stock > 0 ? 'Max in Cart' : 'Sold Out';
    } else if (addBtn.textContent === 'Sold Out' || addBtn.textContent === 'Max in Cart') {
      addBtn.disabled = false;
      addBtn.textContent = 'Add to Cart';
    }
  }

  // ---- Add to Cart buttons ----
  document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.shop-card');
      const qtyEl = card.querySelector('.qty-value');
      const requestedQty = parseInt(qtyEl?.textContent || '1', 10);

      // Check for variation select
      const select = card.querySelector('.shop-variation');
      let variationId = btn.dataset.variationId;
      let price = parseInt(btn.dataset.price, 10);
      let name = btn.dataset.productName;

      if (select) {
        const opt = select.options[select.selectedIndex];
        variationId = opt.value;
        price = parseInt(opt.dataset.price, 10);
        name += ' — ' + opt.textContent.split('—')[0].trim();
      }

      const stock = getSelectedVariationStock(card);
      const alreadyInCart = getCartQtyForVariation(variationId);
      const remaining = Math.max(0, stock - alreadyInCart);

      if (remaining <= 0) {
        alert('This item is fully allocated in your cart.');
        syncQtyControl(card);
        return;
      }

      const qty = Math.min(requestedQty, remaining);
      if (qty < requestedQty) {
        alert('Only ' + remaining + ' more available for this item right now.');
      }

      const added = addToCart(variationId, name, price, qty);
      if (!added) {
        alert('This item is fully allocated in your cart.');
        syncQtyControl(card);
        return;
      }

      syncQtyControl(card);

      // Visual feedback
      const origText = btn.textContent;
      btn.textContent = 'Added ✓';
      btn.classList.add('added');
      setTimeout(() => {
        btn.textContent = origText;
        btn.classList.remove('added');
      }, 1200);
    });
  });

  // ---- Quantity controls ----
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.shop-card');
      const qtyEl = btn.closest('.qty-control').querySelector('.qty-value');
      let val = parseInt(qtyEl.textContent, 10);

      const stock = getSelectedVariationStock(card);
      const addBtn = card.querySelector('.add-to-cart');
      const alreadyInCart = getCartQtyForVariation(addBtn?.dataset.variationId || '');
      const remaining = Math.max(0, stock - alreadyInCart);

      if (btn.dataset.action === 'increase' && val < remaining) val++;
      if (btn.dataset.action === 'decrease' && val > 1) val--;
      qtyEl.textContent = val;
      syncQtyControl(card);
    });
  });

  // ---- Variation select change ----
  document.querySelectorAll('.shop-variation').forEach(sel => {
    sel.addEventListener('change', () => {
      const card = sel.closest('.shop-card');
      const opt = sel.options[sel.selectedIndex];
      const priceEl = card.querySelector('.shop-card-price');
      const stockEl = card.querySelector('.shop-card-stock');
      const addBtn = card.querySelector('.add-to-cart');

      const price = parseInt(opt.dataset.price, 10);
      const inStock = opt.dataset.inStock === '1';

      if (priceEl) priceEl.textContent = '$' + (price / 100).toFixed(2);

      if (stockEl) {
        stockEl.textContent = inStock ? 'In Stock' : 'Out of Stock';
        stockEl.className = 'shop-card-stock ' + (inStock ? 'in' : 'out');
      }

      if (addBtn) {
        addBtn.dataset.variationId = opt.value;
        addBtn.dataset.price = price;
        addBtn.dataset.stock = opt.dataset.stock || '0';
        addBtn.disabled = !inStock;
        addBtn.textContent = inStock ? 'Add to Cart' : 'Sold Out';
      }

      syncQtyControl(card);
    });
  });

  // ---- Checkout ----
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', async () => {
      const cart = getCart();
      if (cart.length === 0) return;

      const shippingMethod = shippingSelect ? shippingSelect.value : 'standard';

      checkoutBtn.disabled = true;
      checkoutBtn.textContent = 'Processing...';

      try {
        const resp = await fetch('/api/square-checkout.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            items: cart.map(i => ({
              variation_id: i.variationId,
              quantity: i.qty,
            })),
            shipping_method: shippingMethod,
          }),
        });

        const data = await resp.json();

        if (data.checkout_url) {
          clearCart();
          window.location.href = data.checkout_url;
        } else {
          alert(data.error || 'Something went wrong. Please try again.');
          checkoutBtn.disabled = false;
          checkoutBtn.textContent = 'Proceed to Checkout';
        }
      } catch {
        alert('Unable to connect. Please try again.');
        checkoutBtn.disabled = false;
        checkoutBtn.textContent = 'Proceed to Checkout';
      }
    });
  }

  if (shippingSelect) {
    shippingSelect.addEventListener('change', renderCart);
  }

  document.querySelectorAll('.shop-card').forEach(card => syncQtyControl(card));

  // ---- Clear cart on successful checkout return ----
  if (window.location.search.includes('checkout=complete')) {
    clearCart();
  }

  // ---- Scroll reveal (reuse from main.js pattern) ----
  const reveals = document.querySelectorAll('.shop-card.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    reveals.forEach(el => obs.observe(el));
  }

  // ---- Helpers ----
  function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  // Initial render
  renderCart();

  // Re-render cart when another script (e.g. product.js) updates localStorage
  window.addEventListener('storage', (e) => {
    if (e.key === 'wds_cart') renderCart();
  });
})();
