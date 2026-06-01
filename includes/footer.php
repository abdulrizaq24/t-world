    <footer class="site-footer">
      <div class="footer-content">
        <div class="footer-brand">
          <a class="brand" href="<?= h(base_path('index.php')) ?>">T-World</a>
          <p>Clean, comfortable T-shirts for everyday style.</p>
        </div>

        <div class="footer-links">
          <div>
            <h2>Shop</h2>
            <a href="<?= h(base_path('index.php')) ?>">Home</a>
            <a href="<?= h(base_path('pages/shop.php')) ?>">Shop</a>
            <a href="<?= h(base_path('pages/cart.php')) ?>">Cart</a>
            <a href="<?= h(base_path('auth/login.php')) ?>">Login</a>
          </div>

          <div>
            <h2>Support</h2>
            <a href="#">Contact</a>
            <a href="#">Shipping</a>
            <a href="#">Returns</a>
            <a href="#">Size Guide</a>
          </div>
        </div>
      </div>

      <p class="footer-bottom">&copy; 2026 T-World. All rights reserved.</p>
    </footer>
    <script src="<?= h(base_path('js/script.js')) ?>"></script>
  </body>
</html>
