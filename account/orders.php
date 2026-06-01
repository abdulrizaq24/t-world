<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$user = require_customer();

if (($user['role'] ?? '') === 'admin') {
    redirect_to('../admin/dashboard.php');
}

global $pdo;

$statement = $pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
$statement->execute(['user_id' => $user['id']]);
$orders = $statement->fetchAll();
$statuses = order_statuses();
$statusCounts = array_fill_keys(array_keys($statuses), 0);
$totalSpent = 0.0;

foreach ($orders as $order) {
    if (isset($statusCounts[$order['status']])) {
        $statusCounts[$order['status']]++;
    }

    $totalSpent += (float) $order['total'];
}

$pageTitle = 'T-World | My Orders';
$pageCss = ['account.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="account-page">
        <div class="account-heading split-heading">
          <div>
            <p class="eyebrow">Account</p>
            <h1>My Orders</h1>
            <p>Track your T-World orders and review your purchase history.</p>
          </div>
          <a class="btn btn-secondary" href="../auth/logout.php">Logout</a>
        </div>

        <nav class="account-tabs" aria-label="Account pages">
          <a href="profile.php">Profile</a>
          <a class="active" href="orders.php">Orders</a>
        </nav>

        <div class="account-stats order-status-summary">
          <article class="account-stat">
            <span>Total Orders</span>
            <strong><?= h((string) count($orders)) ?></strong>
          </article>
          <article class="account-stat">
            <span>Total Spent</span>
            <strong><?= h(money($totalSpent)) ?></strong>
          </article>
          <?php foreach ($statuses as $statusKey => $statusLabel): ?>
            <article class="account-stat compact">
              <span><?= h($statusLabel) ?></span>
              <strong><?= h((string) $statusCounts[$statusKey]) ?></strong>
            </article>
          <?php endforeach; ?>
        </div>

        <?php if (count($orders) === 0): ?>
          <div class="account-empty">
            <h2>No orders yet</h2>
            <p>When you place an order, it will appear here.</p>
            <a class="btn btn-primary" href="../pages/shop.php">Go to Shop</a>
          </div>
        <?php else: ?>
          <div class="account-table-wrap">
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
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
