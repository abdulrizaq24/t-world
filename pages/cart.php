<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Cart';
$pageCss = ['cart.css'];
$items = cart_items();

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="cart-page">
        <div class="cart-heading">
          <p class="eyebrow">Shopping bag</p>
          <h1>Your Cart</h1>
        </div>

        <div class="cart-layout">
          <form class="cart-items" method="post" action="../actions/update_cart.php" aria-label="Cart items">
            <?php if (count($items) === 0): ?>
              <p class="empty-cart-message">
                Your cart is empty. Start by adding a T-shirt from the shop.
              </p>
            <?php endif; ?>

            <?php foreach ($items as $cartKey => $item): ?>
              <article class="cart-item">
                <div class="cart-item-image image-placeholder" data-label="Product photo">
                  <img
                    src="<?= h(base_path($item['image_url'])) ?>"
                    alt="<?= h($item['name']) ?>"
                    loading="lazy"
                    onerror="this.hidden = true"
                  />
                </div>

                <div class="cart-item-details">
                  <h2><?= h($item['name']) ?></h2>
                  <p>Size: <?= h($item['size']) ?></p>
                  <button type="submit" form="remove-<?= h(md5($cartKey)) ?>">Remove</button>
                </div>

                <div class="cart-item-quantity">
                  <label for="quantity-<?= h(md5($cartKey)) ?>">Qty</label>
                  <input
                    id="quantity-<?= h(md5($cartKey)) ?>"
                    type="number"
                    name="quantities[<?= h($cartKey) ?>]"
                    min="1"
                    max="<?= h($item['stock']) ?>"
                    value="<?= h((string) $item['quantity']) ?>"
                  />
                </div>

                <p class="cart-item-price"><?= h(money((float) $item['price'] * (int) $item['quantity'])) ?></p>
              </article>
            <?php endforeach; ?>

            <?php if (count($items) > 0): ?>
              <button class="btn btn-secondary update-cart-button" type="submit">Update Cart</button>
            <?php endif; ?>
          </form>

          <?php foreach ($items as $cartKey => $item): ?>
            <form id="remove-<?= h(md5($cartKey)) ?>" method="post" action="../actions/remove_from_cart.php" hidden>
              <input type="hidden" name="cart_key" value="<?= h($cartKey) ?>" />
            </form>
          <?php endforeach; ?>

          <aside class="order-summary" aria-label="Order summary">
            <h2>Order Summary</h2>

            <div class="summary-row">
              <span>Subtotal</span>
              <span><?= h(money(cart_subtotal())) ?></span>
            </div>
            <div class="summary-row">
              <span>Shipping</span>
              <span><?= h(money(cart_shipping())) ?></span>
            </div>
            <div class="summary-row">
              <span>Discount</span>
              <span>-$0.00</span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span><?= h(money(cart_total())) ?></span>
            </div>

            <a class="btn btn-primary" href="checkout.php">Checkout</a>
            <a class="continue-shopping" href="shop.php">Continue Shopping</a>
          </aside>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
