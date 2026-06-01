<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$user = require_admin();

global $pdo;

$totalOrders = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$revenue = (float) $pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders')->fetchColumn();
$customers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$recentOrders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 5')->fetchAll();
$products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
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
          <a href="#orders">Orders</a>
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

        <section class="stats-grid" aria-label="Store statistics">
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
        </section>

        <section class="admin-section" id="orders">
          <div class="section-title">
            <h2>Recent Orders</h2>
            <a href="#">View all</a>
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
                  <tr>
                    <td><?= h($product['name']) ?></td>
                    <td><?= h($product['category']) ?></td>
                    <td><?= h((string) $product['stock']) ?></td>
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
                          <input type="hidden" name="id" value="<?= h((string) $product['id']) ?>" />
                          <button type="submit">
                            <?= (int) $product['is_active'] === 1 ? 'Hide' : 'Show' ?>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  </body>
</html>
