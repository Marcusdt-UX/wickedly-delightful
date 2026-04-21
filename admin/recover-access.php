<?php
/**
 * Owner self-service recovery page.
 * Allows reset of Square token + merchant lock when valid recovery key is provided.
 */
session_start();
require_once __DIR__ . '/config.php';

$justReset = isset($_GET['done']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = trim((string)($_POST['recovery_key'] ?? ''));

    if ($key === '') {
        $error = 'Enter your recovery key.';
    } elseif (!verify_recovery_key($key)) {
        // Small delay makes brute-force attempts less effective.
        usleep(450000);
        $error = 'Invalid recovery key.';
    } else {
        clear_square_connection();
        session_unset();
        session_destroy();
        header('Location: /admin/login.php?reset=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recover Admin Access — Wickedly Delightful Scents</title>
  <link rel="icon" type="image/png" href="/assets/logo.png" />
  <link rel="stylesheet" href="/css/styles.css" />
  <style>
    .recover-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .recover-card { width: 100%; max-width: 520px; padding: 2rem; border-radius: 0.5rem; }
    .recover-title { font-size: 1.35rem; margin-bottom: 0.5rem; letter-spacing: 0.08em; }
    .recover-sub { color: var(--muted); font-size: 0.88rem; margin-bottom: 1.25rem; line-height: 1.5; }
    .recover-input { width: 100%; padding: 0.8rem 0.9rem; border: 1px solid var(--border); border-radius: 0.35rem; background: var(--background); color: var(--foreground); font-size: 0.95rem; margin-bottom: 0.9rem; }
    .recover-btn { width: 100%; padding: 0.85rem 1rem; border: 1px solid rgba(231,76,60,0.4); border-radius: 0.35rem; background: rgba(231,76,60,0.08); color: #ffb3ab; font-family: 'Cinzel', serif; letter-spacing: 0.08em; cursor: pointer; }
    .recover-note { margin-top: 0.9rem; color: var(--muted); font-size: 0.78rem; line-height: 1.4; }
    .recover-error { margin-bottom: 0.9rem; padding: 0.7rem 0.8rem; border-radius: 0.35rem; border: 1px solid rgba(231,76,60,0.4); color: #e74c3c; background: rgba(231,76,60,0.08); font-size: 0.85rem; }
    .recover-success { margin-bottom: 0.9rem; padding: 0.7rem 0.8rem; border-radius: 0.35rem; border: 1px solid rgba(46,204,113,0.35); color: #2ecc71; background: rgba(46,204,113,0.08); font-size: 0.85rem; }
  </style>
</head>
<body>
  <div class="grain-overlay" aria-hidden="true">
    <svg width="100%" height="100%"><filter id="noise"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="4" stitchTiles="stitch" /><feColorMatrix type="saturate" values="0" /></filter><rect width="100%" height="100%" filter="url(#noise)" /></svg>
  </div>

  <div class="recover-page">
    <div class="recover-card ornate-card ornate-corners">
      <span class="corner-bl"></span><span class="corner-br"></span>
      <h1 class="recover-title debossed-text text-glow-red">Recover Admin Access</h1>
      <p class="recover-sub">Use your owner recovery key to reset the Square connection and unlock admin for a new Square account login.</p>

      <?php if ($error): ?>
      <div class="recover-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <?php if ($justReset): ?>
      <div class="recover-success">Connection reset complete. You can now sign in with the new Square account.</div>
      <?php endif; ?>

      <form method="POST" action="/admin/recover-access.php">
        <label for="recovery_key" class="sr-only">Recovery key</label>
        <input id="recovery_key" class="recover-input input-neumorphic" name="recovery_key" type="text" autocomplete="off" placeholder="Enter recovery key" required />
        <button class="recover-btn btn-neumorphic" type="submit">Reset And Unlock Square Connection</button>
      </form>

      <p class="recover-note">After reset, go to <a href="/admin/login.php" style="color:var(--accent);">admin login</a> and authorize the desired Square account.</p>
      <p class="recover-note"><a href="/admin/login.php" style="color:var(--muted);">Back to login</a></p>
    </div>
  </div>
</body>
</html>
