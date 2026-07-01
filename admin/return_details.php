<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

global $pdo;

$returnId = (int) ($_GET['id'] ?? 0);
$statement = $pdo->prepare(
    'SELECT return_requests.*, orders.customer_name, orders.email, orders.phone, orders.address, orders.city, orders.postal_code, orders.total, orders.status AS order_status
     FROM return_requests
     INNER JOIN orders ON return_requests.order_id = orders.id
     WHERE return_requests.id = :id'
);
$statement->execute(['id' => $returnId]);
$returnRequest = $statement->fetch();

if (!$returnRequest) {
    redirect_to('returns.php');
}

$returnStatuses = return_statuses();
$orderStatuses = order_statuses();
$items = get_order_items((int) $returnRequest['order_id']);
$adminSuccess = flash('admin_success');
$adminError = flash('admin_error');

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | Return #<?= h($returnRequest['id']) ?></title>
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
          <a href="orders.php">Orders</a>
          <a class="active" href="returns.php">Returns</a>
          <a href="customers.php">Customers</a>
          <a href="dashboard.php#products">Products</a>
          <a href="../pages/shop.php">View Store</a>
          <a href="../auth/logout.php">Logout</a>
        </nav>
      </aside>

      <main class="admin-main">
        <header class="admin-header">
          <div>
            <p class="eyebrow">Return details</p>
            <h1>#RT-<?= h($returnRequest['id']) ?></h1>
          </div>
          <a class="btn btn-secondary" href="returns.php">Back to Returns</a>
        </header>

        <?php if ($adminSuccess): ?>
          <p class="admin-message success-message"><?= h($adminSuccess) ?></p>
        <?php endif; ?>

        <?php if ($adminError): ?>
          <p class="admin-message error-message"><?= h($adminError) ?></p>
        <?php endif; ?>

        <section class="admin-detail-grid">
          <article class="admin-section">
            <h2>Request</h2>
            <p><strong>Order:</strong> <a href="order_details.php?id=<?= h($returnRequest['order_id']) ?>">#TW-<?= h($returnRequest['order_id']) ?></a></p>
            <p><strong>Customer:</strong> <?= h($returnRequest['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= h($returnRequest['email']) ?></p>
            <p><strong>Requested:</strong> <?= h(date('M j, Y g:ia', strtotime($returnRequest['created_at']))) ?></p>
            <p><strong>Reason:</strong></p>
            <p class="return-note"><?= nl2br(h($returnRequest['reason'])) ?></p>
          </article>

          <article class="admin-section">
            <h2>Return Status</h2>
            <form class="admin-form" method="post" action="../actions/update_return_status.php">
              <?= csrf_input() ?>
              <input type="hidden" name="return_id" value="<?= h($returnRequest['id']) ?>" />
              <label>
                Status
                <select name="status">
                  <?php foreach ($returnStatuses as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $returnRequest['status'] === $value ? 'selected' : '' ?>>
                      <?= h($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label>
                Admin note
                <textarea name="admin_note" placeholder="Example: Return approved. Send the item back in original condition."><?= h($returnRequest['admin_note']) ?></textarea>
              </label>
              <button class="btn btn-primary" type="submit">Update Return</button>
            </form>
          </article>
        </section>

        <section class="admin-detail-grid">
          <article class="admin-section">
            <h2>Shipping</h2>
            <p><strong>Status:</strong> <span class="status <?= h($returnRequest['order_status']) ?>"><?= h($orderStatuses[$returnRequest['order_status']] ?? $returnRequest['order_status']) ?></span></p>
            <p><strong>Phone:</strong> <?= h($returnRequest['phone']) ?></p>
            <p><strong>Address:</strong> <?= h($returnRequest['address']) ?>, <?= h($returnRequest['city']) ?> <?= h($returnRequest['postal_code']) ?></p>
          </article>

          <article class="admin-section">
            <h2>Order Total</h2>
            <p><strong>Total:</strong> <?= h(money((float) $returnRequest['total'])) ?></p>
          </article>
        </section>

        <section class="admin-section">
          <div class="section-title">
            <h2>Returned Items</h2>
            <span class="status <?= h($returnRequest['status']) ?>"><?= h($returnStatuses[$returnRequest['status']] ?? $returnRequest['status']) ?></span>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Size</th>
                  <th>Quantity</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td><?= h($item['product_name']) ?></td>
                    <td><?= h($item['size']) ?></td>
                    <td><?= h($item['quantity']) ?></td>
                    <td><?= h(money((float) $item['price'])) ?></td>
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
