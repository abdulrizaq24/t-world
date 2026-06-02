<?php

$pageTitle = $pageTitle ?? 'T-World';
$pageCss = $pageCss ?? [];
$basePath = $basePath ?? '';
$user = current_user();
$cartCount = cart_count();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= h($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= h(base_path('css/style.css') . '?v=' . filemtime(__DIR__ . '/../css/style.css')) ?>" />
    <?php foreach ($pageCss as $cssFile): ?>
      <link rel="stylesheet" href="<?= h(base_path('css/' . $cssFile) . '?v=' . filemtime(__DIR__ . '/../css/' . $cssFile)) ?>" />
    <?php endforeach; ?>
  </head>
  <body>
    <header class="site-header">
      <nav class="navbar" aria-label="Main navigation">
        <a class="brand" href="<?= h(base_path('index.php')) ?>">T-World</a>

        <ul class="nav-links">
          <li><a href="<?= h(base_path('index.php')) ?>">Home</a></li>
          <li><a href="<?= h(base_path('pages/shop.php')) ?>">Shop</a></li>
          <li><a href="<?= h(base_path('index.php#categories')) ?>">Categories</a></li>
        </ul>

        <div class="nav-actions">
          <a class="icon-link" href="<?= h(base_path('pages/shop.php#shop-search-area')) ?>" aria-label="Search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="11" cy="11" r="7"></circle>
              <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
            </svg>
          </a>
          <a class="icon-link cart-link" href="<?= h(base_path('pages/cart.php')) ?>" aria-label="Cart, <?= h((string) $cartCount) ?> items">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path
                d="M1 1h4l2.5 13.5a2 2 0 0 0 2 1.5h8.8a2 2 0 0 0 2-1.6L22 6H6"
              ></path>
            </svg>
            <?php if ($cartCount > 0): ?>
              <span class="cart-badge"><?= h((string) $cartCount) ?></span>
            <?php endif; ?>
          </a>
          <a class="icon-link" href="<?= h(base_path($user ? (($user['role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'account/profile.php') : 'auth/login.php')) ?>" aria-label="<?= $user ? 'Account' : 'Login' ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="12" cy="8" r="4"></circle>
              <path d="M4 22c1.5-4 4.3-6 8-6s6.5 2 8 6"></path>
            </svg>
          </a>
        </div>
      </nav>
    </header>


