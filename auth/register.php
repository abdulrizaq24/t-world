<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'T-World | Register';
$pageCss = ['auth.css'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_token_is_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Your session security token expired. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $passwordError = password_validation_error($password);

        if ($name === '' || $email === '' || $password === '') {
            $error = 'Please fill in all fields.';
        } elseif (!email_is_valid($email)) {
            $error = 'Please enter a valid email address.';
        } elseif ($passwordError) {
            $error = $passwordError;
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            global $pdo;

            $statement = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $statement->execute(['email' => $email]);

            if ($statement->fetch()) {
                $error = 'An account with that email already exists.';
            } else {
                $insert = $pdo->prepare(
                    'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
                );
                $insert->execute([
                    'name' => $name,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => (int) $pdo->lastInsertId(),
                    'name' => $name,
                    'email' => $email,
                    'role' => 'customer',
                ];

                redirect_to('../index.php');
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="auth-page">
        <div class="auth-panel">
          <p class="eyebrow">Join T-World</p>
          <h1>Create Account</h1>
          <p class="auth-intro">Create an account to save orders and checkout faster.</p>

          <?php if ($error): ?>
            <p class="auth-error"><?= h($error) ?></p>
          <?php endif; ?>

          <form class="auth-form" method="post">
            <?= csrf_input() ?>
            <label>
              Full name
              <input type="text" name="name" value="<?= h($name ?? '') ?>" required />
            </label>

            <label>
              Email address
              <input type="email" name="email" value="<?= h($email ?? '') ?>" placeholder="you@example.com" required />
            </label>

            <label>
              Password
              <input type="password" name="password" required />
            </label>

            <label>
              Confirm password
              <input type="password" name="confirm_password" required />
            </label>

            <button class="btn btn-primary" type="submit">Create Account</button>
          </form>

          <p class="auth-switch">
            Already have an account?
            <a href="login.php">Login</a>
          </p>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>