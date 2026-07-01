<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

global $pdo;

$customerSearch = trim($_GET['search'] ?? '');
$params = [];
$perPage = 10;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$searchSql = '';

$baseCustomerSql = "
    SELECT * FROM (
        SELECT
            CONCAT('user-', users.id) AS customer_key,
            users.id AS user_id,
            users.name,
            users.email,
            users.phone,
            users.city,
            users.created_at,
            1 AS is_registered,
            COUNT(orders.id) AS total_orders,
            COALESCE(SUM(orders.total), 0) AS total_spent,
            MAX(orders.created_at) AS latest_order_date
        FROM users
        LEFT JOIN orders ON orders.user_id = users.id
        WHERE users.role = 'customer'
        GROUP BY users.id, users.name, users.email, users.phone, users.city, users.created_at

        UNION ALL

        SELECT
            CONCAT('guest-', MD5(orders.email)) AS customer_key,
            NULL AS user_id,
            MAX(orders.customer_name) AS name,
            orders.email,
            MAX(orders.phone) AS phone,
            MAX(orders.city) AS city,
            MIN(orders.created_at) AS created_at,
            0 AS is_registered,
            COUNT(orders.id) AS total_orders,
            COALESCE(SUM(orders.total), 0) AS total_spent,
            MAX(orders.created_at) AS latest_order_date
        FROM orders
        LEFT JOIN users ON users.email = orders.email
        WHERE orders.user_id IS NULL AND users.id IS NULL
        GROUP BY orders.email
    ) AS customers
";

if ($customerSearch !== '') {
    $searchSql = ' WHERE (name LIKE :search OR email LIKE :search OR phone LIKE :search)';
    $params['search'] = '%' . $customerSearch . '%';
}

$countStatement = $pdo->prepare('SELECT COUNT(*) FROM (' . $baseCustomerSql . $searchSql . ') AS counted_customers');
$countStatement->execute($params);
$totalCustomers = (int) $countStatement->fetchColumn();
$totalPages = max(1, (int) ceil($totalCustomers / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$sql = $baseCustomerSql . $searchSql . '
        ORDER BY latest_order_date DESC, created_at DESC
        LIMIT :limit OFFSET :offset';

$statement = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
}

$statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
$statement->bindValue(':offset', $offset, PDO::PARAM_INT);
$statement->execute();
$customers = $statement->fetchAll();
$pagination = pagination_links('customers.php', [
    'search' => $customerSearch,
], $currentPage, $totalPages);

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>T-World | Admin Customers</title>
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
            <p class="eyebrow">Admin</p>
            <h1>Customers</h1>
          </div>
          <a class="btn btn-secondary" href="dashboard.php">Dashboard</a>
        </header>

        <section class="admin-section">
          <div class="section-title">
            <h2>Customer Management</h2>
            <span><?= h((string) $totalCustomers) ?> total</span>
          </div>

          <form class="admin-filters two-field-filter" method="get">
            <label>
              Search
              <input type="search" name="search" value="<?= h($customerSearch) ?>" placeholder="Name, email, or phone" />
            </label>

            <div class="filter-actions">
              <button class="btn btn-primary" type="submit">Filter</button>
              <a class="btn btn-secondary" href="customers.php">Reset</a>
            </div>
          </form>

          <?php if (count($customers) === 0): ?>
            <p class="admin-empty">No customers match those filters.</p>
          <?php else: ?>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($customers as $customer): ?>
                    <tr>
                      <td><?= h($customer['name']) ?></td>
                      <td><?= h($customer['email']) ?></td>
                      <td><span class="status <?= (int) $customer['is_registered'] === 1 ? 'delivered' : 'processing' ?>"><?= (int) $customer['is_registered'] === 1 ? 'Account' : 'Guest' ?></span></td>
                      <td><?= h($customer['phone'] ?: 'Not saved') ?></td>
                      <td><?= h($customer['city'] ?: 'Not saved') ?></td>
                      <td><?= h((string) $customer['total_orders']) ?></td>
                      <td><?= h(money((float) $customer['total_spent'])) ?></td>
                      <td><?= h(date('M j, Y', strtotime($customer['created_at']))) ?></td>
                      <td>
                        <div class="table-actions">
                          <?php if ((int) $customer['is_registered'] === 1): ?>
                            <a href="customer_details.php?id=<?= h($customer['user_id']) ?>">Details</a>
                          <?php endif; ?>
                          <a href="orders.php?search=<?= urlencode((string) $customer['email']) ?>&status=all">Orders</a>
                          <form method="post" action="../actions/delete_customer.php" onsubmit="return confirm('Delete this customer and their order history?');">
                            <?= csrf_input() ?>
                            <input type="hidden" name="customer_type" value="<?= (int) $customer['is_registered'] === 1 ? 'account' : 'guest' ?>" />
                            <input type="hidden" name="user_id" value="<?= h($customer['user_id']) ?>" />
                            <input type="hidden" name="email" value="<?= h($customer['email']) ?>" />
                            <button class="danger-link" type="submit">Delete</button>
                          </form>
                        </div>
                      </td>
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
