<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Login';
$pageCss = ['auth.css'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="auth-page">
        <div class="auth-panel">
          <p class="eyebrow">Welcome back</p>
          <h1>Login</h1>
          <p class="auth-intro">Sign in to manage your T-World orders.</p>

          <?php if ($error): ?>
            <p class="auth-error"><?= h($error) ?></p>
          <?php endif; ?>

          <form class="auth-form" method="post">
            <label>
              Email address
              <input type="email" name="email" value="<?= h($email ?? '') ?>" placeholder="you@example.com" required />
            </label>

            <label>
              Password
              <input type="password" name="password" required />
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
