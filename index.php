<?php

$basePath = '';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'T-World | Home';
$pageCss = ['home.css'];
$featuredProducts = array_slice(get_products(), 0, 3);

require_once __DIR__ . '/includes/header.php';

?>
    <main>
      <section class="hero">
        <div class="hero-content">
          <p class="eyebrow">New collection</p>
          <h1>Wear Your Style</h1>
          <p class="hero-text">
            Discover clean, comfortable T-shirts made for everyday confidence.
          </p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="<?= h(base_path('pages/shop.php')) ?>">Shop Now</a>
            <a class="btn btn-secondary" href="#categories">View Categories</a>
          </div>
        </div>

        <div class="hero-visual image-placeholder" data-label="Add hero photo">
          <img
            src="<?= h(base_path('images/hero-shirt-placeholder.jpg')) ?>"
            alt="Featured T-shirt from T-World"
            onerror="this.hidden = true"
          />
        </div>
      </section>

      <section class="featured-products">
        <div class="section-heading">
          <p class="eyebrow">Featured products</p>
          <h2>Popular T-Shirts</h2>
          <p>Start with these customer favorites from the latest T-World drop.</p>
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
          <p class="eyebrow">Shop by category</p>
          <h2>Find Your Fit</h2>
          <p>Explore T-shirts by style and choose the look that matches your day.</p>
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
          <p class="eyebrow">Limited drop</p>
          <h2>Get 15% off your first T-World order</h2>
          <p>
            Build your everyday rotation with fresh tees made for comfort,
            confidence, and clean style.
          </p>
          <a class="btn btn-light" href="<?= h(base_path('pages/shop.php')) ?>">Shop the Drop</a>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
