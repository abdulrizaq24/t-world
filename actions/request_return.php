<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$user = require_customer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../account/orders.php');
}

require_valid_csrf('../account/orders.php', 'profile_error');

global $pdo;

$orderId = (int) ($_POST['order_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

$statement = $pdo->prepare('SELECT * FROM orders WHERE id = :id AND user_id = :user_id');
$statement->execute([
    'id' => $orderId,
    'user_id' => $user['id'],
]);
$order = $statement->fetch();

if (!$order) {
    redirect_to('../account/orders.php');
}

if ($reason === '') {
    set_flash('return_error', 'Please enter a reason for your return request.');
    redirect_to('../account/order_details.php?id=' . $orderId);
}

if (!in_array($order['status'], ['shipped', 'delivered'], true)) {
    set_flash('return_error', 'Returns can be requested after an order has shipped.');
    redirect_to('../account/order_details.php?id=' . $orderId);
}

if (get_order_return_request($orderId)) {
    set_flash('return_error', 'A return request already exists for this order.');
    redirect_to('../account/order_details.php?id=' . $orderId);
}

$insert = $pdo->prepare(
    'INSERT INTO return_requests (order_id, user_id, reason) VALUES (:order_id, :user_id, :reason)'
);
$insert->execute([
    'order_id' => $orderId,
    'user_id' => $user['id'],
    'reason' => $reason,
]);

set_flash('return_success', 'Your return request has been submitted.');
redirect_to('../account/order_details.php?id=' . $orderId);
