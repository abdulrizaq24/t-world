<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

global $pdo;

$customerId = (int) ($_GET['id'] ?? 0);

$customerStatement = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role = 'customer'");
$customerStatement->execute(['id' => $customerId]);
$customer = $customerStatement->fetch();

if (!$customer) {
    redirect_to('customers.php');
}

$ordersStatement = $pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
$ordersStatement->execute(['user_id' => $customerId]);
$orders = $ordersStatement->fetchAll();

$totalSpent = 0.0;
$statusCounts = array_fill_keys(array_keys(order_statuses()), 0);

foreach ($orders as $order) {
    $totalSpent += (float) $order['total'];

    if (isset($statusCounts[$order['status']])) {
        $statusCounts[$order['status']]++;
    }
}

$latestOrder = $orders[0] ?? null;
$statuses = order_statuses();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | <?= h($customer['name']) ?></title>
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
          <a href="returns.php">Returns</a>
          <a class="active" href="customers.php">Customers</a>
          <a href="dashboard.php#products">Products</a>
          <a href="../pages/shop.php">View Store</a>
          <a href="../auth/logout.php">Logout</a>
        </nav>
      </aside>

      <main class="admin-main">
        <header class="admin-header">
          <div>
            <p class="eyebrow">Customer details</p>
            <h1><?= h($customer['name']) ?></h1>
          </div>
          <a class="btn btn-secondary" href="customers.php">Back to Customers</a>
        </header>

        <section class="stats-grid customer-stats" aria-label="Customer statistics">
          <article class="stat-card">
            <span>Total Orders</span>
            <strong><?= h((string) count($orders)) ?></strong>
          </article>
          <article class="stat-card">
            <span>Total Spent</span>
            <strong><?= h(money($totalSpent)) ?></strong>
          </article>
          <article class="stat-card">
            <span>Latest Order</span>
            <strong><?= $latestOrder ? '#TW-' . h($latestOrder['id']) : 'None' ?></strong>
          </article>
          <article class="stat-card">
            <span>Joined</span>
            <strong><?= h(date('M j, Y', strtotime($customer['created_at']))) ?></strong>
          </article>
        </section>

        <section class="admin-detail-grid customer-detail-grid">
          <article class="admin-section">
            <h2>Profile</h2>
            <p><strong>Name:</strong> <?= h($customer['name']) ?></p>
            <p><strong>Email:</strong> <?= h($customer['email']) ?></p>
            <p><strong>Phone:</strong> <?= h($customer['phone'] ?: 'Not saved') ?></p>
            <p><strong>Joined:</strong> <?= h(date('M j, Y', strtotime($customer['created_at']))) ?></p>
          </article>

          <article class="admin-section">
            <h2>Saved Shipping</h2>
            <p><strong>Address:</strong> <?= h($customer['address'] ?: 'Not saved') ?></p>
            <p><strong>City:</strong> <?= h($customer['city'] ?: 'Not saved') ?></p>
            <p><strong>Postal code:</strong> <?= h($customer['postal_code'] ?: 'Not saved') ?></p>
          </article>
        </section>

        <section class="admin-section">
          <div class="section-title">
            <h2>Orders</h2>
            <?php if ($latestOrder): ?>
              <span class="status <?= h($latestOrder['status']) ?>"><?= h($statuses[$latestOrder['status']] ?? $latestOrder['status']) ?></span>
            <?php endif; ?>
          </div>

          <?php if (count($orders) === 0): ?>
            <p class="admin-empty">This customer has not placed any orders yet.</p>
          <?php else: ?>
            <div class="customer-status-row">
              <?php foreach ($statuses as $statusKey => $statusLabel): ?>
                <span><?= h($statusLabel) ?>: <strong><?= h((string) $statusCounts[$statusKey]) ?></strong></span>
              <?php endforeach; ?>
            </div>

            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td>#TW-<?= h($order['id']) ?></td>
                      <td><span class="status <?= h($order['status']) ?>"><?= h($statuses[$order['status']] ?? $order['status']) ?></span></td>
                      <td><?= h(money((float) $order['total'])) ?></td>
                      <td><?= h(date('M j, Y', strtotime($order['created_at']))) ?></td>
                      <td><a href="order_details.php?id=<?= h($order['id']) ?>">View</a></td>
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
