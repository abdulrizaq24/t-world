<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Returns';
$pageCss = ['support.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="support-page">
        <div class="support-heading">
          <p class="eyebrow">Support</p>
          <h1>Returns</h1>
          <p>If something is not right, here is the basic return process.</p>
        </div>

        <div class="support-grid">
          <article class="support-panel">
            <h2>Return Window</h2>
            <p>Items can be requested for return within 14 days of delivery.</p>
          </article>

          <article class="support-panel">
            <h2>Condition</h2>
            <p>Returned items should be unworn, unwashed, and in their original condition.</p>
          </article>

          <article class="support-panel">
            <h2>How To Start</h2>
            <ol>
              <li>Contact support with your order number.</li>
              <li>Tell us which item you want to return.</li>
              <li>Wait for return instructions before sending the item.</li>
            </ol>
          </article>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>