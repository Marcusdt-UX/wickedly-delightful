<?php
session_start();
require_once __DIR__ . '/config.php';

// Already logged in? Go to dashboard
if (!empty($_SESSION['square_merchant_id']) && !empty($_SESSION['logged_in_at'])) {
    if ((time() - $_SESSION['logged_in_at']) < SESSION_LIFETIME) {
        header('Location: /admin/');
        exit;
    }
}

// Generate CSRF state for OAuth
$state = bin2hex(random_bytes(20));
$_SESSION['oauth_state'] = $state;

// Sandbox CSRF token
$sandboxCsrf = bin2hex(random_bytes(20));
$_SESSION['sandbox_csrf'] = $sandboxCsrf;

$expired = isset($_GET['expired']);
$authUrl = square_authorize_url($state);
$isSandbox = SQUARE_ENV === 'sandbox';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin — Wickedly Delightful Scents</title>
  <link rel="icon" type="image/png" href="/assets/logo.png" />
  <link rel="stylesheet" href="/css/styles.css" />
  <style>
    .login-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      position: relative;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      padding: 2.5rem;
      border-radius: 0.5rem;
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .login-logo {
      width: 80px;
      height: 80px;
      margin: 0 auto 1.5rem;
      border-radius: 50%;
      overflow: hidden;
      border: 2px solid var(--border);
    }

    .login-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .login-title {
      font-family: 'Cinzel', serif;
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
      letter-spacing: 0.1em;
    }

    .login-subtitle {
      font-size: 0.85rem;
      color: var(--muted);
      margin-bottom: 2rem;
      line-height: 1.5;
    }

    .login-divider {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin: 1.5rem 0;
      color: var(--muted);
      font-size: 0.75rem;
      letter-spacing: 0.1em;
    }

    .login-divider::before,
    .login-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: linear-gradient(to right, transparent, var(--border), transparent);
    }

    .square-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      width: 100%;
      padding: 0.9rem 1.5rem;
      background: var(--primary);
      color: var(--primary-foreground);
      border: none;
      border-radius: 0.35rem;
      font-family: 'Cinzel', serif;
      font-size: 0.9rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .square-btn:hover {
      background: var(--accent);
      box-shadow: 0 0 25px rgba(155, 17, 30, 0.6);
    }

    .square-btn svg {
      width: 1.3rem;
      height: 1.3rem;
      fill: currentColor;
    }

    .login-notice {
      margin-top: 1.5rem;
      padding: 0.75rem 1rem;
      background: rgba(231, 76, 60, 0.1);
      border: 1px solid rgba(231, 76, 60, 0.25);
      border-radius: 0.35rem;
      font-size: 0.8rem;
      color: #e74c3c;
    }

    .login-footer {
      margin-top: 2rem;
      font-size: 0.75rem;
      color: var(--muted);
    }

    .login-footer a {
      color: var(--accent);
      text-decoration: none;
      border-bottom: 1px solid transparent;
      transition: border-color 0.2s;
    }

    .login-footer a:hover {
      border-bottom-color: var(--accent);
    }

    .login-config-warning {
      margin-bottom: 1.5rem;
      padding: 1rem;
      background: rgba(241, 196, 15, 0.1);
      border: 1px solid rgba(241, 196, 15, 0.25);
      border-radius: 0.35rem;
      font-size: 0.8rem;
      color: #f1c40f;
      line-height: 1.5;
    }
  </style>
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

  <div class="login-page">
    <div class="leather-texture" aria-hidden="true"></div>

    <div class="login-card ornate-card ornate-corners">
      <span class="corner-bl"></span><span class="corner-br"></span>

      <div class="login-logo">
        <img src="/assets/logo.png" alt="Wickedly Delightful Scents" />
      </div>

      <h1 class="login-title debossed-text text-glow-red">Admin</h1>
      <p class="login-subtitle">Sign in with your Square account to manage your shop</p>

      <?php if (!SQUARE_APP_ID || !SQUARE_APP_SECRET): ?>
      <div class="login-config-warning">
        Square OAuth not configured yet. Add your Application ID and Secret to <code>admin/config.php</code>.
      </div>
      <?php endif; ?>

      <?php if ($expired): ?>
      <div class="login-notice">Your session has expired. Please sign in again.</div>
      <?php endif; ?>

      <div class="login-divider">AUTHENTICATE</div>

      <?php if ($isSandbox): ?>
      <form method="POST" action="/admin/sandbox-login.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($sandboxCsrf); ?>" />
        <button type="submit" class="square-btn btn-neumorphic">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M18.75 1.5H5.25A3.75 3.75 0 0 0 1.5 5.25v13.5A3.75 3.75 0 0 0 5.25 22.5h13.5a3.75 3.75 0 0 0 3.75-3.75V5.25A3.75 3.75 0 0 0 18.75 1.5zm-2.4 13.35a1.5 1.5 0 0 1-1.5 1.5H9.15a1.5 1.5 0 0 1-1.5-1.5V9.15a1.5 1.5 0 0 1 1.5-1.5h5.7a1.5 1.5 0 0 1 1.5 1.5v5.7z"/>
          </svg>
          Sign in with Square (Sandbox)
        </button>
      </form>
      <?php else: ?>
      <a href="<?php echo htmlspecialchars($authUrl); ?>" class="square-btn btn-neumorphic" <?php echo (!SQUARE_APP_ID || !SQUARE_APP_SECRET) ? 'style="opacity:0.4;pointer-events:none;"' : ''; ?>>
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M18.75 1.5H5.25A3.75 3.75 0 0 0 1.5 5.25v13.5A3.75 3.75 0 0 0 5.25 22.5h13.5a3.75 3.75 0 0 0 3.75-3.75V5.25A3.75 3.75 0 0 0 18.75 1.5zm-2.4 13.35a1.5 1.5 0 0 1-1.5 1.5H9.15a1.5 1.5 0 0 1-1.5-1.5V9.15a1.5 1.5 0 0 1 1.5-1.5h5.7a1.5 1.5 0 0 1 1.5 1.5v5.7z"/>
        </svg>
        Sign in with Square
      </a>
      <?php endif; ?>

      <div class="login-footer">
        <a href="/">&larr; Back to site</a>
      </div>
    </div>
  </div>

</body>
</html>
