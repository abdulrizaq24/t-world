<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$user = require_admin();

global $pdo;

$productSearch = trim($_GET['product_search'] ?? '');
$productCategory = $_GET['product_category'] ?? 'all';
$productStatus = $_GET['product_status'] ?? 'all';
$categories = product_categories();
$lowStockLimit = 5;
$productParams = [];
$productSql = 'SELECT * FROM products WHERE 1 = 1';

if ($productSearch !== '') {
    $productSql .= ' AND (name LIKE :product_search OR description LIKE :product_search)';
    $productParams['product_search'] = '%' . $productSearch . '%';
}

if ($productCategory !== 'all' && array_key_exists($productCategory, $categories)) {
    $productSql .= ' AND category = :product_category';
    $productParams['product_category'] = $productCategory;
}

if ($productStatus === 'active') {
    $productSql .= ' AND is_active = 1';
} elseif ($productStatus === 'hidden') {
    $productSql .= ' AND is_active = 0';
} elseif ($productStatus === 'low_stock') {
    $productSql .= ' AND stock <= :low_stock_limit';
    $productParams['low_stock_limit'] = $lowStockLimit;
}

$productSql .= ' ORDER BY created_at DESC';
$productStatement = $pdo->prepare($productSql);
$productStatement->execute($productParams);
$products = $productStatement->fetchAll();

$totalOrders = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$revenue = (float) $pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders')->fetchColumn();
$customers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$lowStockCountStatement = $pdo->prepare('SELECT COUNT(*) FROM products WHERE stock <= :low_stock_limit');
$lowStockCountStatement->execute(['low_stock_limit' => $lowStockLimit]);
$lowStockCount = (int) $lowStockCountStatement->fetchColumn();
$recentOrders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 5')->fetchAll();
$adminSuccess = flash('admin_success');
$adminError = flash('admin_error');

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
  </head>
  <body>
    <div class="admin-layout">
      <aside class="admin-sidebar">
        <a class="brand" href="../index.php">T-World</a>

        <nav class="admin-nav" aria-label="Admin navigation">
          <a class="active" href="dashboard.php">Dashboard</a>
          <a href="orders.php">Orders</a>
          <a href="returns.php">Returns</a>
          <a href="customers.php">Customers</a>
          <a href="#products">Products</a>
          <a href="../pages/shop.php">View Store</a>
          <a href="../auth/logout.php">Logout</a>
        </nav>
      </aside>

      <main class="admin-main">
        <header class="admin-header">
          <div>
            <p class="eyebrow">Admin</p>
            <h1>Dashboard</h1>
          </div>
          <a class="btn btn-primary" href="product_form.php">Add Product</a>
        </header>

        <?php if ($adminSuccess): ?>
          <p class="admin-message success-message"><?= h($adminSuccess) ?></p>
        <?php endif; ?>

        <?php if ($adminError): ?>
          <p class="admin-message error-message"><?= h($adminError) ?></p>
        <?php endif; ?>

        <section class="stats-grid dashboard-stats" aria-label="Store statistics">
          <article class="stat-card">
            <span>Total Orders</span>
            <strong><?= h((string) $totalOrders) ?></strong>
          </article>
          <article class="stat-card">
            <span>Total Products</span>
            <strong><?= h((string) $totalProducts) ?></strong>
          </article>
          <article class="stat-card">
            <span>Revenue</span>
            <strong><?= h(money($revenue)) ?></strong>
          </article>
          <article class="stat-card">
            <span>Customers</span>
            <strong><?= h((string) $customers) ?></strong>
          </article>
          <article class="stat-card warning-card">
            <span>Low Stock</span>
            <strong><?= h((string) $lowStockCount) ?></strong>
          </article>
        </section>

        <section class="admin-section" id="orders">
          <div class="section-title">
            <h2>Recent Orders</h2>
            <a href="orders.php">View all</a>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentOrders as $order): ?>
                  <tr>
                    <td>#TW-<?= h((string) $order['id']) ?></td>
                    <td><?= h($order['customer_name']) ?></td>
                    <td><span class="status <?= h($order['status']) ?>"><?= h(ucfirst($order['status'])) ?></span></td>
                    <td><?= h(money((float) $order['total'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="admin-section" id="products">
          <div class="section-title">
            <h2>Product Management</h2>
            <a class="btn btn-secondary" href="product_form.php">New Product</a>
          </div>

          <form class="admin-filters" method="get" action="dashboard.php#products">
            <label>
              Search
              <input type="search" name="product_search" value="<?= h($productSearch) ?>" placeholder="Product name" />
            </label>

            <label>
              Category
              <select name="product_category">
                <option value="all">All categories</option>
                <?php foreach ($categories as $value => $label): ?>
                  <option value="<?= h($value) ?>" <?= $productCategory === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label>
              Status
              <select name="product_status">
                <option value="all">All statuses</option>
                <option value="active" <?= $productStatus === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="hidden" <?= $productStatus === 'hidden' ? 'selected' : '' ?>>Hidden</option>
                <option value="low_stock" <?= $productStatus === 'low_stock' ? 'selected' : '' ?>>Low stock</option>
              </select>
            </label>

            <div class="filter-actions">
              <button class="btn btn-primary" type="submit">Filter</button>
              <a class="btn btn-secondary" href="dashboard.php#products">Reset</a>
            </div>
          </form>

          <?php if (count($products) === 0): ?>
            <p class="admin-empty">No products match those filters.</p>
          <?php else: ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($products as $product): ?>
                    <?php $isLowStock = (int) $product['stock'] <= $lowStockLimit; ?>
                    <tr class="<?= $isLowStock ? 'low-stock-row' : '' ?>">
                      <td><?= h($product['name']) ?></td>
                      <td><?= h($categories[$product['category']] ?? $product['category']) ?></td>
                      <td>
                        <?= h((string) $product['stock']) ?>
                        <?php if ($isLowStock): ?>
                          <span class="stock-warning">Low</span>
                        <?php endif; ?>
                      </td>
                      <td><?= h(money((float) $product['price'])) ?></td>
                      <td>
                        <span class="status <?= (int) $product['is_active'] === 1 ? 'shipped' : 'pending' ?>">
                          <?= (int) $product['is_active'] === 1 ? 'Active' : 'Hidden' ?>
                        </span>
                      </td>
                      <td>
                        <div class="table-actions">
                          <a href="product_form.php?id=<?= h((string) $product['id']) ?>">Edit</a>
                          <form method="post" action="../actions/toggle_product.php">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id" value="<?= h((string) $product['id']) ?>" />
                            <button type="submit">
                              <?= (int) $product['is_active'] === 1 ? 'Hide' : 'Show' ?>
                            </button>
                          </form>
                          <form method="post" action="../actions/delete_product.php" onsubmit="return confirm('Delete this product? This only works if it has no order history.');">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id" value="<?= h((string) $product['id']) ?>" />
                            <button class="danger-link" type="submit">Delete</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>
      </main>
    </div>
  </body>
</html>
