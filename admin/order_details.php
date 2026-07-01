<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$orderId = (int) ($_GET['id'] ?? 0);
$order = get_order($orderId);

if (!$order) {
    redirect_to('orders.php');
}

$items = get_order_items($orderId);
$statuses = order_statuses();
$returnStatuses = return_statuses();
$returnRequest = get_order_return_request($orderId);
$adminSuccess = flash('admin_success');
$adminError = flash('admin_error');

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | Order #<?= h($order['id']) ?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
  </head>
  <body>
    <div class="admin-layout">
      <aside class="admin-sidebar">
        <a class="brand" href="../index.php" aria-label="T-World home">
          <img src="<?= h(base_path('images/favicon.png')) ?>" alt="T-World" />
        </a>

        <nav class="admin-nav" aria-label="Admin navigation">
          <a href="dashboard.php">Dashboard</a>
          <a class="active" href="orders.php">Orders</a>
          <a href="returns.php">Returns</a>
          <a href="customers.php">Customers</a>
          <a href="dashboard.php#products">Products</a>
          <a href="../pages/shop.php">View Store</a>
          <a href="../auth/logout.php">Logout</a>
        </nav>
      </aside>

      <main class="admin-main">
        <header class="admin-header">
          <div>
            <p class="eyebrow">Order details</p>
            <h1>#TW-<?= h($order['id']) ?></h1>
          </div>
          <a class="btn btn-secondary" href="orders.php">Back to Orders</a>
        </header>

        <?php if ($adminSuccess): ?>
          <p class="admin-message success-message"><?= h($adminSuccess) ?></p>
        <?php endif; ?>

        <?php if ($adminError): ?>
          <p class="admin-message error-message"><?= h($adminError) ?></p>
        <?php endif; ?>

        <section class="admin-detail-grid">
          <article class="admin-section">
            <h2>Customer</h2>
            <p><strong>Name:</strong> <?= h($order['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= h($order['email']) ?></p>
            <p><strong>Phone:</strong> <?= h($order['phone']) ?></p>
            <p><strong>Address:</strong> <?= h($order['address']) ?>, <?= h($order['city']) ?> <?= h($order['postal_code']) ?></p>
          </article>

          <article class="admin-section">
            <h2>Status</h2>
            <form class="status-update-form" method="post" action="../actions/update_order_status.php">
              <?= csrf_input() ?>
              <input type="hidden" name="order_id" value="<?= h($order['id']) ?>" />
              <label>
                Order status
                <select name="status">
                  <?php foreach ($statuses as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $order['status'] === $value ? 'selected' : '' ?>>
                      <?= h($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
              <button class="btn btn-primary" type="submit">Update Status</button>
            </form>
          </article>
        </section>

        <?php if ($returnRequest): ?>
          <section class="admin-section">
            <div class="section-title">
              <h2>Return Request</h2>
              <span class="status <?= h($returnRequest['status']) ?>"><?= h($returnStatuses[$returnRequest['status']] ?? $returnRequest['status']) ?></span>
            </div>
            <p><strong>Reason:</strong> <?= nl2br(h($returnRequest['reason'])) ?></p>
            <?php if (!empty($returnRequest['admin_note'])): ?>
              <p class="return-note"><strong>Admin note:</strong><br /><?= nl2br(h($returnRequest['admin_note'])) ?></p>
            <?php endif; ?>
            <a class="btn btn-secondary" href="return_details.php?id=<?= h($returnRequest['id']) ?>">Manage Return</a>
          </section>
        <?php endif; ?>

        <section class="admin-section">
          <div class="section-title">
            <h2>Items</h2>
            <span class="status <?= h($order['status']) ?>"><?= h($statuses[$order['status']] ?? $order['status']) ?></span>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Size</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td><?= h($item['product_name']) ?></td>
                    <td><?= h($item['size']) ?></td>
                    <td><?= h($item['quantity']) ?></td>
                    <td><?= h(money((float) $item['price'])) ?></td>
                    <td><?= h(money((float) $item['price'] * (int) $item['quantity'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="order-totals">
            <div class="summary-row">
              <span>Subtotal</span>
              <span><?= h(money((float) $order['subtotal'])) ?></span>
            </div>
            <div class="summary-row">
              <span>Shipping</span>
              <span><?= h(money((float) $order['shipping'])) ?></span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span><?= h(money((float) $order['total'])) ?></span>
            </div>
          </div>
        </section>
      </main>
    </div>
  </body>
</html>


