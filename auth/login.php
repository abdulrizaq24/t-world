<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Login';
$pageCss = ['auth.css'];
$pageJs = ['password-toggle.js'];
$error = '';
$success = flash('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_token_is_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Your session security token expired. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        global $pdo;

        $statement = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            redirect_to($user['role'] === 'admin' ? '../admin/dashboard.php' : '../index.php');
        }

        $error = 'Invalid email or password.';
    }
}

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="auth-page">
        <div class="auth-panel">
          <p class="eyebrow">Welcome back</p>
          <h1>Sign In</h1>
          <p class="auth-intro">Access your account, track orders, and continue shopping premium everyday essentials.</p>

          <?php if ($success): ?>
            <p class="auth-success"><?= h($success) ?></p>
          <?php endif; ?>

          <?php if ($error): ?>
            <p class="auth-error"><?= h($error) ?></p>
          <?php endif; ?>

          <form class="auth-form" method="post">
            <?= csrf_input() ?>
            <label>
              Email address
              <input type="email" name="email" value="<?= h($email ?? '') ?>" placeholder="you@example.com" required />
            </label>

            <label>
              Password
              <span class="password-field">
                <input type="password" name="password" required data-password-input />
                <button type="button" class="password-toggle" aria-label="Show password" aria-pressed="false" data-password-toggle>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </span>
            </label>

            <button class="btn btn-primary" type="submit">Login</button>
          </form>

          <p class="auth-switch">
            New to T-World?
            <a href="register.php">Create an account</a>
          </p>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>