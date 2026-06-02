<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

global $pdo;

$status = $_GET['status'] ?? 'all';
$statuses = return_statuses();
$where = [];
$params = [];

$sql = 'SELECT return_requests.*, orders.customer_name, orders.email, orders.total, orders.status AS order_status
        FROM return_requests
        INNER JOIN orders ON return_requests.order_id = orders.id';

if ($status !== 'all' && array_key_exists($status, $statuses)) {
    $where[] = 'return_requests.status = :status';
    $params['status'] = $status;
}

if (count($where) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY return_requests.created_at DESC';
$statement = $pdo->prepare($sql);
$statement->execute($params);
$returnRequests = $statement->fetchAll();
$orderStatuses = order_statuses();
$adminSuccess = flash('admin_success');
$adminError = flash('admin_error');

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | Admin Returns</title>
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
            <p class="eyebrow">Admin</p>
            <h1>Returns</h1>
          </div>
          <a class="btn btn-secondary" href="orders.php">Orders</a>
        </header>

        <?php if ($adminSuccess): ?>
          <p class="admin-message success-message"><?= h($adminSuccess) ?></p>
        <?php endif; ?>

        <?php if ($adminError): ?>
          <p class="admin-message error-message"><?= h($adminError) ?></p>
        <?php endif; ?>

        <section class="admin-section">
          <div class="section-title">
            <h2>Return Requests</h2>
            <span><?= h((string) count($returnRequests)) ?> total</span>
          </div>

          <form class="admin-filters two-field-filter" method="get">
            <label>
              Return status
              <select name="status">
                <option value="all">All statuses</option>
                <?php foreach ($statuses as $value => $label): ?>
                  <option value="<?= h($value) ?>" <?= $status === $value ? 'selected' : '' ?>>
                    <?= h($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>

            <div class="filter-actions">
              <button class="btn btn-primary" type="submit">Filter</button>
              <a class="btn btn-secondary" href="returns.php">Reset</a>
            </div>
          </form>

          <?php if (count($returnRequests) === 0): ?>
            <p class="admin-empty">No return requests match that status.</p>
          <?php else: ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Return</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Return Status</th>
                    <th>Order Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($returnRequests as $request): ?>
                    <tr>
                      <td>#RT-<?= h($request['id']) ?></td>
                      <td>#TW-<?= h($request['order_id']) ?></td>
                      <td><?= h($request['customer_name']) ?><br /><?= h($request['email']) ?></td>
                      <td><span class="status <?= h($request['status']) ?>"><?= h($statuses[$request['status']] ?? $request['status']) ?></span></td>
                      <td><span class="status <?= h($request['order_status']) ?>"><?= h($orderStatuses[$request['order_status']] ?? $request['order_status']) ?></span></td>
                      <td><?= h(money((float) $request['total'])) ?></td>
                      <td><?= h(date('M j, Y', strtotime($request['created_at']))) ?></td>
                      <td><a href="return_details.php?id=<?= h($request['id']) ?>">View</a></td>
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
