<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Contact';
$pageCss = ['support.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="support-page">
        <div class="support-heading">
          <p class="eyebrow">Support</p>
          <h1>Contact Us</h1>
          <p>Need help with an order, product, or account? Reach out and we will help you sort it out.</p>
        </div>

        <div class="support-grid">
          <article class="support-panel">
            <h2>Email</h2>
            <p>support@t-world.test</p>
          </article>

          <article class="support-panel">
            <h2>Phone</h2>
            <p>+1 555 000 0000</p>
          </article>

          <article class="support-panel">
            <h2>Support Hours</h2>
            <p>Monday to Friday, 9:00 AM - 5:00 PM.</p>
          </article>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>