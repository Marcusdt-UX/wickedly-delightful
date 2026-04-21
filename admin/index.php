<?php
/**
 * Admin Dashboard — Wickedly Delightful Scents
 * Shows connection status, merchant info, and quick actions.
 */
require_once __DIR__ . '/auth.php';

if (empty($_SESSION['admin_reset_csrf'])) {
  $_SESSION['admin_reset_csrf'] = bin2hex(random_bytes(20));
}
$adminResetCsrf = $_SESSION['admin_reset_csrf'];
$newRecoveryKey = $_SESSION['new_recovery_key'] ?? '';
unset($_SESSION['new_recovery_key']);

// Get token info
$tokenData = get_square_token();
$hasToken = !empty($tokenData['access_token']);
$tokenExpires = $tokenData['expires_at'] ?? 0;
$tokenObtained = $tokenData['obtained_at'] ?? '';

// Check if token still works by pinging merchant endpoint
$connectionOk = false;
$merchantInfo = null;
$locations = [];

if ($hasToken) {
    $accessToken = get_valid_access_token();
    if ($accessToken) {
        $apiBase = SQUARE_ENV === 'production'
            ? 'https://connect.squareup.com/v2'
            : 'https://connect.squareupsandbox.com/v2';

        // Merchant info
        $ch = curl_init($apiBase . '/merchants/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $resp = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (!empty($resp['merchant'])) {
            $connectionOk = true;
            $merchantInfo = $resp['merchant'];
        }

        // Locations
        $ch = curl_init($apiBase . '/locations');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $resp = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $locations = $resp['locations'] ?? [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — Admin — Wickedly Delightful Scents</title>
  <link rel="icon" type="image/png" href="/assets/logo.png" />
  <link rel="stylesheet" href="/css/styles.css" />
  <link rel="stylesheet" href="/admin/assets/admin.css" />
</head>
<body>

  <div class="grain-overlay" aria-hidden="true">
    <svg width="100%" height="100%">
      <filter id="noise">
        <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="4" stitchTiles="stitch" />
        <feColorMatrix type="saturate" values="0" />
      </filter>
      <rect width="100%" height="100%" filter="url(#noise)" />
    </svg>
  </div>

  <!-- Admin Nav -->
  <nav class="nav" id="nav">
    <div class="gradient-line"></div>
    <div class="nav-inner">
      <a href="/admin/" class="nav-logo">
        <img src="/assets/logo.png" alt="Wickedly Delightful Scents" class="nav-logo-img" />
        <span class="nav-logo-text text-glow-red">Admin</span>
      </a>

      <div class="nav-links">
        <a href="/admin/" class="nav-link active">Dashboard</a>
        <a href="/" class="nav-link" target="_blank">View Site</a>
        <a href="/shop.php" class="nav-link" target="_blank">View Shop</a>
      </div>

      <a href="/admin/logout.php" class="nav-link" style="color:var(--muted);font-size:0.85rem;">Sign Out</a>
    </div>
    <div class="gradient-line"></div>
  </nav>

  <main class="admin-main">
    <div class="leather-texture" aria-hidden="true"></div>
    <div class="section-inner">

      <!-- Header -->
      <div class="admin-header">
        <h1 class="section-title debossed-text tracking-luxury" style="font-size:1.6rem;">
          Welcome, <span class="accent text-glow-red"><?php echo htmlspecialchars($merchantName); ?></span>
        </h1>
        <p class="section-subtitle">Manage your Wickedly Delightful Scents shop</p>
      </div>

      <!-- Status Cards -->
      <div class="admin-grid">

        <!-- Connection Status -->
        <div class="admin-card ornate-card ornate-corners">
          <span class="corner-bl"></span><span class="corner-br"></span>
          <div class="admin-card-inner">
            <div class="about-accent-line"></div>
            <h3 class="admin-card-title">Square Connection</h3>

            <?php if ($connectionOk): ?>
            <div class="status-badge status-ok">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              Connected
            </div>
            <?php else: ?>
            <div class="status-badge status-error">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
              Disconnected
            </div>
            <?php endif; ?>

            <?php if ($merchantInfo): ?>
            <div class="admin-detail">
              <span class="admin-label">Business</span>
              <span><?php echo htmlspecialchars($merchantInfo['business_name'] ?? '—'); ?></span>
            </div>
            <div class="admin-detail">
              <span class="admin-label">Country</span>
              <span><?php echo htmlspecialchars($merchantInfo['country'] ?? '—'); ?></span>
            </div>
            <div class="admin-detail">
              <span class="admin-label">Environment</span>
              <span><?php echo SQUARE_ENV; ?></span>
            </div>
            <?php endif; ?>

            <?php if ($tokenExpires): ?>
            <div class="admin-detail">
              <span class="admin-label">Token Expires</span>
              <span><?php echo date('M j, Y', $tokenExpires); ?></span>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Location Info -->
        <div class="admin-card ornate-card ornate-corners">
          <span class="corner-bl"></span><span class="corner-br"></span>
          <div class="admin-card-inner">
            <div class="about-accent-line"></div>
            <h3 class="admin-card-title">Locations</h3>

            <?php if (empty($locations)): ?>
            <p class="admin-muted">No locations found.</p>
            <?php else: ?>
              <?php foreach ($locations as $loc): ?>
              <div class="admin-location <?php echo ($loc['id'] === $locationId) ? 'admin-location--active' : ''; ?>">
                <div class="admin-detail">
                  <span class="admin-label"><?php echo htmlspecialchars($loc['name'] ?? 'Location'); ?></span>
                  <span class="status-dot <?php echo ($loc['status'] ?? '') === 'ACTIVE' ? 'status-dot--ok' : 'status-dot--off'; ?>"></span>
                </div>
                <div class="admin-detail-sm">
                  ID: <code><?php echo htmlspecialchars($loc['id']); ?></code>
                </div>
                <?php if ($loc['id'] === $locationId): ?>
                <div class="admin-detail-sm" style="color:var(--accent);">★ Active for shop</div>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card ornate-card ornate-corners">
          <span class="corner-bl"></span><span class="corner-br"></span>
          <div class="admin-card-inner">
            <div class="about-accent-line"></div>
            <h3 class="admin-card-title">Quick Actions</h3>

            <a href="/admin/cache-clear.php" class="admin-action btn-neumorphic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
              Clear Product Cache
            </a>

            <a href="/shop.php" class="admin-action btn-neumorphic" target="_blank">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Preview Shop
            </a>

            <a href="https://squareup.com/dashboard" class="admin-action btn-neumorphic" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              Square Dashboard
            </a>

            <form method="POST" action="/admin/reset-square-connection.php" onsubmit="return confirm('This will disconnect the current Square account and unlock admin for a new account. Continue?');">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($adminResetCsrf); ?>" />
              <button type="submit" class="admin-action admin-action-danger btn-neumorphic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>
                Reset Square Connection
              </button>
            </form>

            <form method="POST" action="/admin/regenerate-recovery-key.php" onsubmit="return confirm('Generate a new recovery key? The old key will stop working.');">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($adminResetCsrf); ?>" />
              <button type="submit" class="admin-action btn-neumorphic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 1v22"/><path d="M1 12h22"/></svg>
                Regenerate Recovery Key
              </button>
            </form>

            <?php if ($newRecoveryKey): ?>
            <div class="admin-recovery-box">
              <div class="admin-recovery-title">Owner Recovery Key (save now)</div>
              <code class="admin-recovery-key"><?php echo htmlspecialchars($newRecoveryKey); ?></code>
              <p class="admin-help">This key is shown once. Store it in your password manager.</p>
            </div>
            <?php endif; ?>

            <p class="admin-help">Use only when intentionally switching to a different Square account.</p>
          </div>
        </div>

      </div>

    </div>
  </main>

  <script src="/js/main.js"></script>
</body>
</html>
