<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Checkout';
$pageCss = ['checkout.css'];
$items = cart_items();
$user = current_user();
$customer = null;
$orderSuccess = $_SESSION['order_success'] ?? null;
$checkoutError = flash('checkout_error');

if ($user) {
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $statement->execute(['id' => $user['id']]);
    $customer = $statement->fetch() ?: null;
}

$fullName = trim((string) ($customer['name'] ?? $user['name'] ?? ''));
$nameParts = preg_split('/\s+/', $fullName, 2) ?: [];
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';
$email = $customer['email'] ?? $user['email'] ?? '';
$phone = $customer['phone'] ?? '';
$address = $customer['address'] ?? '';
$city = $customer['city'] ?? '';
$postalCode = $customer['postal_code'] ?? '';

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="checkout-page">
        <div class="checkout-heading">
          <p class="eyebrow">Secure checkout</p>
          <h1>Checkout</h1>
        </div>

        <?php if ($checkoutError): ?>
          <div class="form-section checkout-message error-message">
            <p><?= h($checkoutError) ?></p>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $orderSuccess): ?>
          <div class="form-section checkout-message">
            <h2>Order placed</h2>
            <p>Your order #<?= h((string) $orderSuccess) ?> has been received.</p>
          </div>
          <?php unset($_SESSION['order_success']); ?>
        <?php elseif (count($items) === 0): ?>
          <div class="form-section checkout-message">
            <h2>Your cart is empty</h2>
            <p>Add a T-shirt before checking out.</p>
            <a class="btn btn-primary" href="shop.php">Go to Shop</a>
          </div>
        <?php else: ?>
          <div class="checkout-layout">
            <form class="checkout-form" method="post" action="../actions/place_order.php">
              <?= csrf_input() ?>
              <section class="form-section">
                <h2>Contact Information</h2>
                <div class="form-grid">
                  <label>
                    Email address
                    <input type="email" name="email" value="<?= h($email) ?>" placeholder="you@example.com" required />
                  </label>
                  <label>
                    Phone number
                    <input type="tel" name="phone" value="<?= h($phone) ?>" placeholder="+1 555 000 0000" required />
                  </label>
                </div>
              </section>

              <section class="form-section">
                <h2>Shipping Address</h2>
                <div class="form-grid">
                  <label>
                    First name
                    <input type="text" name="first_name" value="<?= h($firstName) ?>" required />
                  </label>
                  <label>
                    Last name
                    <input type="text" name="last_name" value="<?= h($lastName) ?>" required />
                  </label>
                  <label class="full-field">
                    Address
                    <input type="text" name="address" value="<?= h($address) ?>" required />
                  </label>
                  <label>
                    City
                    <input type="text" name="city" value="<?= h($city) ?>" required />
                  </label>
                  <label>
                    Postal code
                    <input type="text" name="postal_code" value="<?= h($postalCode) ?>" required />
                  </label>
                </div>
              </section>

              <section class="form-section">
                <h2>Payment Method</h2>
                <div class="payment-options">
                  <label>
                    <input type="radio" name="payment" value="card" checked />
                    <span>Credit / Debit Card</span>
                  </label>
                  <label>
                    <input type="radio" name="payment" value="cash" />
                    <span>Cash on Delivery</span>
                  </label>
                </div>
              </section>

              <button class="btn btn-primary" type="submit">Place Order</button>
            </form>

            <aside class="checkout-summary" aria-label="Order summary">
              <h2>Order Summary</h2>

              <?php foreach ($items as $item): ?>
                <div class="summary-product">
                  <span><?= h($item['name']) ?> x <?= h((string) $item['quantity']) ?></span>
                  <span><?= h(money((float) $item['price'] * (int) $item['quantity'])) ?></span>
                </div>
              <?php endforeach; ?>

              <div class="summary-row">
                <span>Subtotal</span>
                <span><?= h(money(cart_subtotal())) ?></span>
              </div>
              <div class="summary-row">
                <span>Shipping</span>
                <span><?= h(money(cart_shipping())) ?></span>
              </div>
              <div class="summary-row total">
                <span>Total</span>
                <span><?= h(money(cart_total())) ?></span>
              </div>
            </aside>
          </div>
        <?php endif; ?>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>