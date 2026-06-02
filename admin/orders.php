<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

global $pdo;

$status = $_GET['status'] ?? 'all';
$orderSearch = trim($_GET['search'] ?? '');
$statuses = order_statuses();
$where = [];
$params = [];
$perPage = 10;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$sql = 'SELECT * FROM orders';
$countSql = 'SELECT COUNT(*) FROM orders';

if ($status !== 'all' && array_key_exists($status, $statuses)) {
    $where[] = 'status = :status';
    $params['status'] = $status;
}

if ($orderSearch !== '') {
    $where[] = '(customer_name LIKE :search OR email LIKE :search OR id = :order_id)';
    $params['search'] = '%' . $orderSearch . '%';
    $params['order_id'] = (int) preg_replace('/\D+/', '', $orderSearch);
}

if (count($where) > 0) {
    $whereSql = ' WHERE ' . implode(' AND ', $where);
    $sql .= $whereSql;
    $countSql .= $whereSql;
}

$countStatement = $pdo->prepare($countSql);
$countStatement->execute($params);
$totalOrders = (int) $countStatement->fetchColumn();
$totalPages = max(1, (int) ceil($totalOrders / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
$statement = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $statement->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}

$statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
$statement->bindValue(':offset', $offset, PDO::PARAM_INT);
$statement->execute();
$orders = $statement->fetchAll();
$pagination = pagination_links('orders.php', [
    'search' => $orderSearch,
    'status' => $status,
], $currentPage, $totalPages);
$adminSuccess = flash('admin_success');
$adminError = flash('admin_error');

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | Admin Orders</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
  </head>
  <body>
    <div class="admin-layout">
      <aside class="admin-sidebar">
        <a class="brand" href="../index.php">T-World</a>

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
            <p class="eyebrow">Admin</p>
            <h1>Orders</h1>
          </div>
          <a class="btn btn-secondary" href="dashboard.php">Dashboard</a>
        </header>

        <?php if ($adminSuccess): ?>
          <p class="admin-message success-message"><?= h($adminSuccess) ?></p>
        <?php endif; ?>

        <?php if ($adminError): ?>
          <p class="admin-message error-message"><?= h($adminError) ?></p>
        <?php endif; ?>

        <section class="admin-section">
          <div class="section-title">
            <h2>All Orders</h2>
            <span><?= h((string) $totalOrders) ?> total</span>
          </div>

          <form class="admin-filters" method="get">
            <label>
              Search
              <input type="search" name="search" value="<?= h($orderSearch) ?>" placeholder="Order, name, or email" />
            </label>

            <label>
              Status
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
              <a class="btn btn-secondary" href="orders.php">Reset</a>
            </div>
          </form>

          <?php if (count($orders) === 0): ?>
            <p class="admin-empty">No orders match those filters.</p>
          <?php else: ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Email</th>
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
                      <td><?= h($order['customer_name']) ?></td>
                      <td><?= h($order['email']) ?></td>
                      <td>
                        <form class="inline-status-form" method="post" action="../actions/update_order_status.php">
                          <?= csrf_input() ?>
                          <input type="hidden" name="order_id" value="<?= h($order['id']) ?>" />
                          <input type="hidden" name="redirect_to" value="../admin/orders.php?<?= h(http_build_query(['search' => $orderSearch, 'status' => $status, 'page' => $currentPage])) ?>" />
                          <select name="status" aria-label="Order #TW-<?= h($order['id']) ?> status">
                            <?php foreach ($statuses as $value => $label): ?>
                              <option value="<?= h($value) ?>" <?= $order['status'] === $value ? 'selected' : '' ?>>
                                <?= h($label) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <button type="submit">Update</button>
                        </form>
                      </td>
                      <td><?= h(money((float) $order['total'])) ?></td>
                      <td><?= h(date('M j, Y', strtotime($order['created_at']))) ?></td>
                      <td><a href="order_details.php?id=<?= h($order['id']) ?>">View</a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?= $pagination ?>
          <?php endif; ?>
        </section>
      </main>
    </div>
  </body>
</html>
