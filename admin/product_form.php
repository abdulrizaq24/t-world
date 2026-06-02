<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$productId = (int) ($_GET['id'] ?? 0);
$product = $productId > 0 ? get_admin_product($productId) : null;
$isEditing = (bool) $product;
$error = flash('admin_error');
$categories = product_categories();

if ($productId > 0 && !$product) {
    redirect_to('dashboard.php#products');
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | <?= $isEditing ? 'Edit Product' : 'New Product' ?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
  </head>
  <body>
    <div class="admin-layout">
      <aside class="admin-sidebar">
        <a class="brand" href="../index.php">T-World</a>

        <nav class="admin-nav" aria-label="Admin navigation">
          <a href="dashboard.php">Dashboard</a>
          <a href="orders.php">Orders</a>
          <a href="returns.php">Returns</a>
          <a href="customers.php">Customers</a>
          <a class="active" href="dashboard.php#products">Products</a>
          <a href="../pages/shop.php">View Store</a>
          <a href="../auth/logout.php">Logout</a>
        </nav>
      </aside>

      <main class="admin-main">
        <header class="admin-header">
          <div>
            <p class="eyebrow">Admin</p>
            <h1><?= $isEditing ? 'Edit Product' : 'New Product' ?></h1>
          </div>
          <a class="btn btn-secondary" href="dashboard.php#products">Back</a>
        </header>

        <section class="admin-section">
          <?php if ($error): ?>
            <p class="admin-message error-message"><?= h($error) ?></p>
          <?php endif; ?>

          <form class="admin-form" method="post" action="../actions/save_product.php" enctype="multipart/form-data">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= h($product['id'] ?? '') ?>" />

            <label>
              Product name
              <input type="text" name="name" value="<?= h($product['name'] ?? '') ?>" required />
            </label>

            <label>
              Category
              <select name="category" required>
                <?php foreach ($categories as $slug => $label): ?>
                  <option value="<?= h($slug) ?>" <?= ($product['category'] ?? '') === $slug ? 'selected' : '' ?>>
                    <?= h($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>

            <label>
              Description
              <textarea name="description" rows="5" required><?= h($product['description'] ?? '') ?></textarea>
            </label>

            <div class="admin-form-grid">
              <label>
                Price
                <input type="number" name="price" min="0" step="0.01" value="<?= h($product['price'] ?? '') ?>" required />
              </label>

              <label>
                Stock
                <input type="number" name="stock" min="0" value="<?= h($product['stock'] ?? '0') ?>" required />
              </label>
            </div>

            <?php if (!empty($product['image_url'])): ?>
              <div class="current-product-image image-placeholder" data-label="Current product photo">
                <img
                  src="<?= h(base_path($product['image_url'])) ?>"
                  alt="<?= h($product['name'] ?? 'Product image') ?>"
                  onerror="this.hidden = true"
                />
              </div>
            <?php endif; ?>

            <label>
              Upload product image
              <input type="file" name="product_image" accept="image/jpeg,image/png,image/webp" />
            </label>

            <label>
              Image path
              <input type="text" name="image_url" value="<?= h($product['image_url'] ?? '') ?>" placeholder="uploads/products/example.jpg" />
            </label>

            <label class="checkbox-field">
              <input type="checkbox" name="is_active" value="1" <?= (int) ($product['is_active'] ?? 1) === 1 ? 'checked' : '' ?> />
              Active product
            </label>

            <button class="btn btn-primary" type="submit">Save Product</button>
          </form>
        </section>
      </main>
    </div>
  </body>
</html>


