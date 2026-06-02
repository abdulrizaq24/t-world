<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Size Guide';
$pageCss = ['support.css'];

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="support-page">
        <div class="support-heading">
          <p class="eyebrow">Support</p>
          <h1>Size Guide</h1>
          <p>Use this guide as a simple starting point when choosing your T-shirt size.</p>
        </div>

        <article class="support-panel">
          <h2>T-Shirt Sizes</h2>
          <div class="support-table-wrap">
            <table class="support-table">
              <thead>
                <tr>
                  <th>Size</th>
                  <th>Chest</th>
                  <th>Length</th>
                  <th>Fit</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>S</td>
                  <td>34-36 in</td>
                  <td>26 in</td>
                  <td>Slim / regular</td>
                </tr>
                <tr>
                  <td>M</td>
                  <td>38-40 in</td>
                  <td>28 in</td>
                  <td>Regular</td>
                </tr>
                <tr>
                  <td>L</td>
                  <td>42-44 in</td>
                  <td>29 in</td>
                  <td>Relaxed</td>
                </tr>
                <tr>
                  <td>XL</td>
                  <td>46-48 in</td>
                  <td>30 in</td>
                  <td>Oversized / roomy</td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>