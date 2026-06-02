<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$user = require_customer();
$orderId = (int) ($_GET['id'] ?? 0);

if (($user['role'] ?? '') === 'admin') {
    redirect_to('../admin/order_details.php?id=' . $orderId);
}

global $pdo;

$statement = $pdo->prepare('SELECT * FROM orders WHERE id = :id AND user_id = :user_id');
$statement->execute([
    'id' => $orderId,
    'user_id' => $user['id'],
]);
$order = $statement->fetch();

if (!$order) {
    redirect_to('orders.php');
}

$items = get_order_items($orderId);
$statuses = order_statuses();
$returnStatuses = return_statuses();
$returnRequest = get_order_return_request($orderId);
$returnError = flash('return_error');
$returnSuccess = flash('return_success');
$shippingSteps = ['pending', 'processing', 'shipped', 'delivered'];
$currentStepIndex = array_search($order['status'], $shippingSteps, true);
$canRequestReturn = in_array($order['status'], ['shipped', 'delivered'], true) && !$returnRequest;
$pageTitle = 'T-World | Order #' . $order['id'];
$pageCss = ['account.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="account-page">
        <div class="account-heading split-heading">
          <div>
            <p class="eyebrow">Order details</p>
            <h1>#TW-<?= h($order['id']) ?></h1>
            <p>Placed on <?= h(date('M j, Y', strtotime($order['created_at']))) ?></p>
          </div>
          <a class="btn btn-secondary" href="orders.php">Back to Orders</a>
        </div>

        <nav class="account-tabs" aria-label="Account pages">
          <a href="profile.php">Profile</a>
          <a class="active" href="orders.php">Orders</a>
        </nav>

        <?php if ($returnError): ?>
          <p class="account-message error"><?= h($returnError) ?></p>
        <?php endif; ?>

        <?php if ($returnSuccess): ?>
          <p class="account-message success"><?= h($returnSuccess) ?></p>
        <?php endif; ?>

        <div class="account-detail-grid">
          <article class="account-panel">
            <h2>Shipping Info</h2>
            <p><strong>Name:</strong> <?= h($order['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= h($order['email']) ?></p>
            <p><strong>Phone:</strong> <?= h($order['phone']) ?></p>
            <p><strong>Address:</strong> <?= h($order['address']) ?>, <?= h($order['city']) ?> <?= h($order['postal_code']) ?></p>
          </article>

          <article class="account-panel">
            <h2>Status</h2>
            <span class="status <?= h($order['status']) ?>"><?= h($statuses[$order['status']] ?? $order['status']) ?></span>
          </article>
        </div>

        <article class="account-panel process-panel">
          <h2>Shipping Progress</h2>
          <div class="process-steps">
            <?php foreach ($shippingSteps as $index => $step): ?>
              <div class="process-step <?= $currentStepIndex !== false && $index <= $currentStepIndex ? 'complete' : '' ?>">
                <span><?= h((string) ($index + 1)) ?></span>
                <strong><?= h($statuses[$step]) ?></strong>
              </div>
            <?php endforeach; ?>
          </div>
        </article>

        <article class="account-panel process-panel">
          <h2>Return Process</h2>
          <?php if ($returnRequest): ?>
            <p><strong>Status:</strong> <span class="status <?= h($returnRequest['status']) ?>"><?= h($returnStatuses[$returnRequest['status']] ?? $returnRequest['status']) ?></span></p>
            <p><strong>Reason:</strong> <?= h($returnRequest['reason']) ?></p>
            <?php if (!empty($returnRequest['admin_note'])): ?>
              <p><strong>Admin note:</strong> <?= h($returnRequest['admin_note']) ?></p>
            <?php endif; ?>
          <?php elseif ($canRequestReturn): ?>
            <form class="account-form" method="post" action="../actions/request_return.php">
              <?= csrf_input() ?>
              <input type="hidden" name="order_id" value="<?= h($order['id']) ?>" />
              <label>
                Return reason
                <textarea name="reason" rows="4" required></textarea>
              </label>
              <button class="btn btn-primary" type="submit">Request Return</button>
            </form>
          <?php else: ?>
            <p>Returns can be requested after your order has shipped.</p>
          <?php endif; ?>
        </article>

        <div class="account-panel">
          <h2>Items</h2>
          <div class="account-table-wrap">
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

          <div class="order-totals account-totals">
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
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>