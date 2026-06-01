<?php

$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';

$user = require_customer();

if (($user['role'] ?? '') === 'admin') {
    redirect_to('../admin/dashboard.php');
}

global $pdo;

$statement = $pdo->prepare('SELECT * FROM users WHERE id = :id');
$statement->execute(['id' => $user['id']]);
$account = $statement->fetch();

if (!$account) {
    session_destroy();
    redirect_to('../auth/login.php');
}

$summaryStatement = $pdo->prepare(
    'SELECT COUNT(*) AS total_orders, COALESCE(SUM(total), 0) AS total_spent, MAX(created_at) AS latest_order_date
     FROM orders
     WHERE user_id = :user_id'
);
$summaryStatement->execute(['user_id' => $account['id']]);
$orderSummary = $summaryStatement->fetch() ?: [
    'total_orders' => 0,
    'total_spent' => 0,
    'latest_order_date' => null,
];

$latestOrderStatement = $pdo->prepare(
    'SELECT id, status, total, created_at
     FROM orders
     WHERE user_id = :user_id
     ORDER BY created_at DESC
     LIMIT 1'
);
$latestOrderStatement->execute(['user_id' => $account['id']]);
$latestOrder = $latestOrderStatement->fetch() ?: null;
$statuses = order_statuses();

$pageTitle = 'T-World | My Profile';
$pageCss = ['account.css'];
$profileError = flash('profile_error') ?? '';
$profileSuccess = '';
$passwordError = '';
$passwordSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf('profile.php', 'profile_error');

    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');

        if ($name === '' || $email === '') {
            $profileError = 'Please enter your name and email.';
        } elseif (!email_is_valid($email)) {
            $profileError = 'Please enter a valid email address.';
        } elseif ($phone !== '' && !phone_is_valid($phone)) {
            $profileError = 'Please enter a valid phone number.';
        } else {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id != :id');
            $check->execute([
                'email' => $email,
                'id' => $account['id'],
            ]);

            if ($check->fetch()) {
                $profileError = 'That email is already used by another account.';
            } else {
                $update = $pdo->prepare(
                    'UPDATE users
                     SET name = :name, email = :email, phone = :phone, address = :address, city = :city, postal_code = :postal_code
                     WHERE id = :id'
                );
                $update->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone !== '' ? $phone : null,
                    'address' => $address !== '' ? $address : null,
                    'city' => $city !== '' ? $city : null,
                    'postal_code' => $postalCode !== '' ? $postalCode : null,
                    'id' => $account['id'],
                ]);

                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $account['name'] = $name;
                $account['email'] = $email;
                $account['phone'] = $phone;
                $account['address'] = $address;
                $account['city'] = $city;
                $account['postal_code'] = $postalCode;
                $profileSuccess = 'Profile updated successfully.';
            }
        }
    }

    if ($formType === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $passwordError = 'Please fill in all password fields.';
        } elseif (!password_verify($currentPassword, $account['password_hash'])) {
            $passwordError = 'Current password is incorrect.';
        } elseif ($validationError = password_validation_error($newPassword)) {
            $passwordError = $validationError;
        } elseif ($newPassword !== $confirmPassword) {
            $passwordError = 'New passwords do not match.';
        } else {
            $update = $pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
            $update->execute([
                'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
                'id' => $account['id'],
            ]);

            $passwordSuccess = 'Password updated successfully.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';

?>
    <main>
      <section class="account-page">
        <div class="account-heading split-heading">
          <div>
            <p class="eyebrow">Account</p>
            <h1>My Profile</h1>
            <p>Manage your T-World account details, saved shipping info, and password.</p>
          </div>
          <a class="btn btn-secondary" href="../auth/logout.php">Logout</a>
        </div>

        <nav class="account-tabs" aria-label="Account pages">
          <a class="active" href="profile.php">Profile</a>
          <a href="orders.php">Orders</a>
        </nav>

        <div class="account-stats">
          <article class="account-stat">
            <span>Total Orders</span>
            <strong><?= h((string) $orderSummary['total_orders']) ?></strong>
          </article>
          <article class="account-stat">
            <span>Total Spent</span>
            <strong><?= h(money((float) $orderSummary['total_spent'])) ?></strong>
          </article>
          <article class="account-stat">
            <span>Latest Order</span>
            <?php if ($latestOrder): ?>
              <strong>#TW-<?= h($latestOrder['id']) ?></strong>
              <a href="order_details.php?id=<?= h($latestOrder['id']) ?>">
                <?= h($statuses[$latestOrder['status']] ?? $latestOrder['status']) ?>
              </a>
            <?php else: ?>
              <strong>None yet</strong>
            <?php endif; ?>
          </article>
        </div>

        <div class="account-form-grid">
          <article class="account-panel">
            <h2>Account Details</h2>

            <?php if ($profileError): ?>
              <p class="account-message error"><?= h($profileError) ?></p>
            <?php endif; ?>

            <?php if ($profileSuccess): ?>
              <p class="account-message success"><?= h($profileSuccess) ?></p>
            <?php endif; ?>

            <form class="account-form" method="post">
              <?= csrf_input() ?>
              <input type="hidden" name="form_type" value="profile" />

              <label>
                Full name
                <input type="text" name="name" value="<?= h($account['name']) ?>" required />
              </label>

              <label>
                Email address
                <input type="email" name="email" value="<?= h($account['email']) ?>" required />
              </label>

              <label>
                Phone number
                <input type="tel" name="phone" value="<?= h($account['phone'] ?? '') ?>" />
              </label>

              <label>
                Address
                <input type="text" name="address" value="<?= h($account['address'] ?? '') ?>" />
              </label>

              <label>
                City
                <input type="text" name="city" value="<?= h($account['city'] ?? '') ?>" />
              </label>

              <label>
                Postal code
                <input type="text" name="postal_code" value="<?= h($account['postal_code'] ?? '') ?>" />
              </label>

              <button class="btn btn-primary" type="submit">Save Profile</button>
            </form>
          </article>

          <article class="account-panel">
            <h2>Change Password</h2>

            <?php if ($passwordError): ?>
              <p class="account-message error"><?= h($passwordError) ?></p>
            <?php endif; ?>

            <?php if ($passwordSuccess): ?>
              <p class="account-message success"><?= h($passwordSuccess) ?></p>
            <?php endif; ?>

            <form class="account-form" method="post">
              <?= csrf_input() ?>
              <input type="hidden" name="form_type" value="password" />

              <label>
                Current password
                <input type="password" name="current_password" required />
              </label>

              <label>
                New password
                <input type="password" name="new_password" required />
              </label>

              <label>
                Confirm new password
                <input type="password" name="confirm_password" required />
              </label>

              <button class="btn btn-primary" type="submit">Update Password</button>
            </form>
          </article>
        </div>
      </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>