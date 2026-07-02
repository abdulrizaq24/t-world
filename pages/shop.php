<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Shop';
$pageCss = ['shop.css'];
$activeCategory = $_GET['category'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$products = get_products($activeCategory, $search);
$filters = [
    'all' => 'All',
    'oversized' => 'Oversized',
    'graphic' => 'Graphic',
    'plain' => 'Plain',
    'new' => 'New Arrivals',
];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="full-width-banner">
        <div class="full-width-banner-content">
          <p class="eyebrow">NEW ARRIVALS</p>
          <h1>SUMMER T-SHIRT COLLECTION</h1>
          <p>
            Premium oversized tees, bold graphics, and everyday essentials.
          </p>
          <a class="btn btn-primary" href="shop.php">SHOP NOW</a>
        </div>
      </section>

      <section class="shop-products" aria-label="Product list">
        <div class="shop-toolbar">
          <form class="shop-controls" method="get" action="shop.php">
            <div class="filter-list" aria-label="Product categories">
              <?php foreach ($filters as $slug => $label): ?>
                <button
                  class="filter-chip <?= $activeCategory === $slug ? 'active' : '' ?>"
                  type="submit"
                  name="category"
                  value="<?= h($slug) ?>"
                  data-filter="<?= h($slug) ?>"
                >
                  <?= h($label) ?>
                </button>
              <?php endforeach; ?>
            </div>

            <label class="shop-search" id="shop-search-area" for="product-search">
              Search products
              <input
                id="product-search"
                type="search"
                name="search"
                value="<?= h($search) ?>"
                placeholder="Search tees, fits, colors"
              />
            </label>
          </form>

          <p class="product-count">Showing <?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?></p>
        </div>

        <div class="shop-grid">
          <?php foreach ($products as $product): ?>
            <article class="product-card" data-category="<?= h($product['category']) ?>" data-name="<?= h($product['name']) ?>">
              <div class="product-image image-placeholder" data-label="Add product photo">
                <img
                  src="<?= h(base_path($product['image_url'])) ?>"
                  alt="<?= h($product['name']) ?>"
                  loading="lazy"
                  onerror="this.hidden = true"
                />
              </div>
              <div class="product-info">
                <h2><?= h($product['name']) ?></h2>
                <p><?= h(money((float) $product['price'])) ?></p>
                <div class="product-card-actions">
                  <a href="product_details.php?id=<?= h((string) $product['id']) ?>">View Product</a>
                  <form method="post" action="../actions/add_to_cart.php">
                    <?= csrf_input() ?>
                    <input type="hidden" name="product_id" value="<?= h((string) $product['id']) ?>" />
                    <input type="hidden" name="size" value="M" />
                    <input type="hidden" name="quantity" value="1" />
                    <button type="submit" <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>>
                      <?= (int) $product['stock'] > 0 ? 'Add' : 'Sold out' ?>
                    </button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

