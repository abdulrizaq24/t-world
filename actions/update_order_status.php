<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../admin/orders.php');
}

$redirectPath = $_POST['redirect_to'] ?? '../admin/orders.php';
if (!is_string($redirectPath) || !str_starts_with($redirectPath, '../admin/')) {
    $redirectPath = '../admin/orders.php';
}

require_valid_csrf($redirectPath);

global $pdo;

$orderId = (int) ($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$orderId || !array_key_exists($status, order_statuses())) {
    set_flash('admin_error', 'Invalid order status.');
    redirect_to($redirectPath);
}

$statement = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
$statement->execute([
    'id' => $orderId,
    'status' => $status,
]);

set_flash('admin_success', 'Order status updated.');
redirect_to($redirectPath);