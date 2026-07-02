<?php

$basePath = '';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'T-World | Home';
$pageCss = ['home.css'];
$featuredProducts = array_slice(get_products(), 0, 3);

require_once __DIR__ . '/includes/header.php';

?>
    <main>
      <section class="full-width-banner">
        <div class="full-width-banner-content">
          <p class="eyebrow">NEW ARRIVALS</p>
          <h1>SUMMER T-SHIRT COLLECTION</h1>
          <p>
            Premium oversized tees, bold graphics, and everyday essentials.
          </p>
          <a class="btn btn-primary" href="<?= h(base_path('pages/shop.php')) ?>">SHOP NOW</a>
        </div>
      </section>

      <section class="featured-products">
        <div class="section-heading">
          <p class="eyebrow">Curated Selection</p>
          <h2>Signature Pieces</h2>
          <p>Our most loved silhouettes, designed to anchor your wardrobe.</p>
        </div>

        <div class="trust-strip" aria-label="Store highlights">
          <div>
            <strong>Premium fabrics</strong>
            <span>Comfort-first cotton blends with elevated structure.</span>
          </div>
          <div>
            <strong>Fast local delivery</strong>
            <span>Quick dispatch on ready-to-wear essentials.</span>
          </div>
          <div>
            <strong>Easy returns</strong>
            <span>Shop confidently with simple support after purchase.</span>
          </div>
        </div>

        <div class="product-grid">
          <?php foreach ($featuredProducts as $product): ?>
            <article class="product-card">
              <div class="product-image image-placeholder" data-label="Add product photo">
                <img
                  src="<?= h(base_path($product['image_url'])) ?>"
                  alt="<?= h($product['name']) ?>"
                  loading="lazy"
                  onerror="this.hidden = true"
                />
              </div>
              <div class="product-info">
                <h3><?= h($product['name']) ?></h3>
                <p><?= h(money((float) $product['price'])) ?></p>
                <a href="<?= h(base_path('pages/product_details.php?id=' . $product['id'])) ?>">View Product</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="categories" id="categories">
        <div class="section-heading">
          <p class="eyebrow">Collections</p>
          <h2>Find Your Signature Fit</h2>
          <p>From structured heavyweight to relaxed everyday drapes.</p>
        </div>

        <div class="category-grid">
          <?php
            $categories = [
                ['label' => 'Oversized Tees', 'slug' => 'oversized', 'image' => 'images/category-oversized.jpg'],
                ['label' => 'Graphic Tees', 'slug' => 'graphic', 'image' => 'images/category-graphic.jpg'],
                ['label' => 'Plain Tees', 'slug' => 'plain', 'image' => 'images/category-plain.jpg'],
                ['label' => 'New Arrivals', 'slug' => 'new', 'image' => 'images/category-new.jpg'],
            ];
          ?>
          <?php foreach ($categories as $category): ?>
            <a class="category-card image-placeholder" href="<?= h(base_path('pages/shop.php?category=' . $category['slug'])) ?>" data-label="Add category photo">
              <img
                src="<?= h(base_path($category['image'])) ?>"
                alt="<?= h($category['label']) ?>"
                loading="lazy"
                onerror="this.hidden = true"
              />
              <span><?= h($category['label']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="promo-banner">
        <div class="promo-content">
          <p class="eyebrow">Join the club</p>
          <h2>Unlock 15% off your first order</h2>
          <p>
            Upgrade your rotation with premium essentials. Sign up for early access to drops and exclusive offers.
          </p>
          <a class="btn btn-light" href="<?= h(base_path('pages/shop.php')) ?>">Shop Essentials</a>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
