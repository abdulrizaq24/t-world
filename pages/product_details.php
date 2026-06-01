<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$productId = (int) ($_GET['id'] ?? 1);
$product = get_product($productId);
$cartError = flash('cart_error');

if (!$product) {
    redirect_to('shop.php');
}

$pageTitle = 'T-World | ' . $product['name'];
$pageCss = ['product.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="product-detail">
        <div class="product-gallery">
          <div class="main-product-image image-placeholder" data-label="Add product photo">
            <img
              src="<?= h(base_path($product['image_url'])) ?>"
              alt="<?= h($product['name']) ?>"
              onerror="this.hidden = true"
            />
          </div>

          <div class="thumbnail-row" aria-label="Product image thumbnails">
            <?php for ($i = 1; $i <= 3; $i++): ?>
              <div class="thumbnail image-placeholder" data-label="Photo">
                <img
                  src="<?= h(base_path($product['image_url'])) ?>"
                  alt="<?= h($product['name'] . ' view ' . $i) ?>"
                  loading="lazy"
                  onerror="this.hidden = true"
                />
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="product-summary">
          <p class="eyebrow"><?= h($product['category']) ?> collection</p>
          <h1><?= h($product['name']) ?></h1>
          <p class="product-price"><?= h(money((float) $product['price'])) ?></p>
          <p class="product-description"><?= h($product['description']) ?></p>

          <?php if ($cartError): ?>
            <p class="product-message error-message"><?= h($cartError) ?></p>
          <?php endif; ?>

          <form class="product-form" method="post" action="../actions/add_to_cart.php">
            <?= csrf_input() ?>
            <input type="hidden" name="product_id" value="<?= h((string) $product['id']) ?>" />

            <fieldset>
              <legend>Select size</legend>
              <div class="size-options">
                <?php foreach (['S', 'M', 'L', 'XL'] as $size): ?>
                  <label>
                    <input type="radio" name="size" value="<?= h($size) ?>" <?= $size === 'M' ? 'checked' : '' ?> />
                    <span><?= h($size) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </fieldset>

            <label class="quantity-field" for="quantity">
              Quantity
              <input
                id="quantity"
                type="number"
                name="quantity"
                min="1"
                max="<?= h((string) max(1, (int) $product['stock'])) ?>"
                value="1"
                <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>
              />
            </label>

            <button class="btn btn-primary" type="submit" <?= (int) $product['stock'] <= 0 ? 'disabled' : '' ?>>
              <?= (int) $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
            </button>
          </form>

          <div class="product-notes">
            <h2>Details</h2>
            <ul>
              <li>Stock available: <?= h((string) $product['stock']) ?></li>
              <li>Category: <?= h($product['category']) ?></li>
              <li>Machine washable</li>
              <li>Made for everyday wear</li>
            </ul>
          </div>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
