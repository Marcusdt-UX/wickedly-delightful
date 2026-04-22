# Wickedly Delightful Scents Handoff

Last updated: 2026-04-22

## Live URLs

- Main site: https://wickedlydelightfulscents.shop/
- Shop: https://wickedlydelightfulscents.shop/shop.php
- Admin login: https://wickedlydelightfulscents.shop/admin/login.php
- Admin recovery: https://wickedlydelightfulscents.shop/admin/recover-access.php
- Test site: https://wickedly.thomaspublishinghouse.com/

## What Is Live

- Marketing homepage with branded design and contact section.
- Shop page with Square-backed product catalog and cart drawer.
- Individual product detail pages for catalog items.
- Square hosted checkout for shipped orders and local pickup.
- Inventory-aware cart behavior that prevents adding more than available stock.
- Admin area for Square OAuth connection status, cache clearing, and recovery-key management.

## Checkout Behavior

The storefront currently uses these checkout methods:

- Standard Shipping: $8.95
- Express Shipping: $15.95
- Local Pickup: $0.00

Implementation notes:

- Shipping and pickup are sent to Square using explicit order fulfillments.
- Shipped orders request a shipping address in Square checkout.
- Pickup orders do not ask for a shipping address.
- Cart quantities are validated against live Square inventory before checkout is created.
- Existing cart items are normalized in the browser so stale local carts cannot exceed current stock.

## Admin Access

Admin sign-in is done through Square OAuth at the admin login URL.

After the first successful connection:

- The storefront becomes locked to that Square merchant account.
- An Owner Recovery Key is generated and shown once on the admin dashboard.
- That recovery key should be stored in the client password manager immediately.

Available admin actions:

- View Square connection status
- See active Square location information
- Clear product cache
- Preview shop
- Open Square Dashboard
- Reset Square connection
- Regenerate recovery key

## Recovery And Account Switching

If the wrong Square account was connected, or the client needs to intentionally switch accounts:

1. Go to the admin recovery page.
2. Enter the saved Owner Recovery Key.
3. This clears the stored token and merchant lock.
4. Return to admin login and authorize the intended Square account.

If the client is already logged into admin, they can also use Reset Square Connection from the dashboard.

## Day-One Client Checklist

1. Sign in at the admin login URL with the client Square account.
2. Save the Owner Recovery Key when it appears.
3. Confirm the correct Square location is marked active in admin.
4. Review products in Square and confirm only intended items are ready for sale.
5. Open the shop and test one item through checkout for:
   - Standard shipping
   - Express shipping
   - Local pickup
6. Verify post-checkout redirect returns to the shop successfully.

## Content And Operations Notes

- Product data comes from Square.
- Product inventory comes from Square.
- If catalog changes do not appear immediately, use Clear Product Cache in admin.
- The homepage contact form is intentionally deferred for a future Formspree implementation.
- Homepage footer social links are configured for Facebook and email (Instagram intentionally omitted).
- The footer currently routes support questions to the homepage contact section instead of external social links.
- If the catalog is temporarily empty, the shop now shows a real empty-state message instead of a loading spinner.

## Verified Before Handoff

- Product pages load correctly from shop links.
- Cart drawer updates from both shop and product pages.
- Cart quantity cannot exceed live inventory after reload or stale local cart state.
- Square checkout shipping display no longer shows contradictory free-shipping messaging.
- Pickup checkout hides shipping address and shipping method UI.
- Shop and product footers no longer contain dead placeholder social links.
- Homepage footer social links now point to live Facebook and email destinations.

## Support Boundaries

The codebase already contains the Square OAuth application setup and storefront behavior needed for launch. The client should not need code changes for normal use.

Normal client-side maintenance should be limited to:

- managing products and inventory in Square
- logging into admin
- clearing cache when needed
- keeping the recovery key stored safely

## Repo Notes

- Storefront root: this repository
- Admin files: /admin
- Storefront pages: /index.html, /shop.php, /product.php
- Square storefront API files: /api
- Frontend scripts: /js
- Frontend styles: /css
