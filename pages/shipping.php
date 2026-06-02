<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Shipping';
$pageCss = ['support.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="support-page">
        <div class="support-heading">
          <p class="eyebrow">Support</p>
          <h1>Shipping</h1>
          <p>Here is how T-World handles delivery after your order is placed.</p>
        </div>

        <div class="support-grid">
          <article class="support-panel">
            <h2>Processing Time</h2>
            <p>Orders are usually prepared within 1-2 business days.</p>
          </article>

          <article class="support-panel">
            <h2>Delivery Time</h2>
            <p>Standard delivery usually takes 3-7 business days after processing.</p>
          </article>

          <article class="support-panel">
            <h2>Shipping Cost</h2>
            <p>Standard shipping is currently shown during checkout before you place your order.</p>
          </article>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>