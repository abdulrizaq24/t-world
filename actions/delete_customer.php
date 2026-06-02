<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../admin/customers.php');
}

require_valid_csrf('../admin/customers.php');

global $pdo;

$customerType = $_POST['customer_type'] ?? '';
$userId = (int) ($_POST['user_id'] ?? 0);
$email = trim($_POST['email'] ?? '');

try {
    $pdo->beginTransaction();

    if ($customerType === 'account' && $userId > 0) {
        $customerStatement = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'customer'");
        $customerStatement->execute(['id' => $userId]);

        if (!$customerStatement->fetch()) {
            throw new RuntimeException('Customer account was not found.');
        }

        $ordersStatement = $pdo->prepare('SELECT id FROM orders WHERE user_id = :user_id');
        $ordersStatement->execute(['user_id' => $userId]);
        $orderIds = $ordersStatement->fetchAll(PDO::FETCH_COLUMN);

        if (count($orderIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
            $deleteOrders = $pdo->prepare('DELETE FROM orders WHERE id IN (' . $placeholders . ')');
            $deleteOrders->execute($orderIds);
        }

        $deleteCustomer = $pdo->prepare('DELETE FROM users WHERE id = :id AND role = :role');
        $deleteCustomer->execute([
            'id' => $userId,
            'role' => 'customer',
        ]);
    } elseif ($customerType === 'guest' && email_is_valid($email)) {
        $deleteGuestOrders = $pdo->prepare('DELETE FROM orders WHERE user_id IS NULL AND email = :email');
        $deleteGuestOrders->execute(['email' => $email]);
    } else {
        throw new RuntimeException('Invalid customer delete request.');
    }

    $pdo->commit();
    set_flash('admin_success', 'Customer deleted.');
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    set_flash('admin_error', $exception->getMessage());
}

redirect_to('../admin/customers.php');
