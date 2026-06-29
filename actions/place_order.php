<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$cart = cart_items();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || count($cart) === 0) {
    redirect_to('../pages/cart.php');
}

require_valid_csrf('../pages/checkout.php', 'checkout_error');

global $pdo;

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$customerName = trim($firstName . ' ' . $lastName);
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$postalCode = trim($_POST['postal_code'] ?? '');
$user = current_user();

if ($customerName === '' || $email === '' || $phone === '' || $address === '' || $city === '' || $postalCode === '') {
    set_flash('checkout_error', 'Please complete all checkout fields.');
    redirect_to('../pages/checkout.php');
}

if (!email_is_valid($email)) {
    set_flash('checkout_error', 'Please enter a valid email address.');
    redirect_to('../pages/checkout.php');
}

if (!phone_is_valid($phone)) {
    set_flash('checkout_error', 'Please enter a valid phone number.');
    redirect_to('../pages/checkout.php');
}

try {
    $pdo->beginTransaction();

    $stockStatement = $pdo->prepare('SELECT id, name, price, stock FROM products WHERE id = :id AND is_active = 1 FOR UPDATE');
    $freshCart = [];

    foreach ($cart as $cartKey => $item) {
        $stockStatement->execute(['id' => $item['product_id']]);
        $product = $stockStatement->fetch();

        if (!$product || (int) $product['stock'] < (int) $item['quantity']) {
            throw new RuntimeException('Some items are no longer available in the requested quantity.');
        }

        $freshCart[$cartKey] = [
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'size' => $item['size'],
            'quantity' => (int) $item['quantity'],
        ];
    }

    $subtotal = array_reduce($freshCart, function (float $sum, array $item): float {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0.0);
    $shipping = $subtotal > 0 ? 5.00 : 0.00;
    $total = $subtotal + $shipping;

    $orderStatement = $pdo->prepare(
        'INSERT INTO orders (user_id, customer_name, email, phone, address, city, postal_code, subtotal, shipping, total)
         VALUES (:user_id, :customer_name, :email, :phone, :address, :city, :postal_code, :subtotal, :shipping, :total)'
    );
    $orderStatement->execute([
        'user_id' => $user['id'] ?? null,
        'customer_name' => $customerName,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'postal_code' => $postalCode,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $total,
    ]);

    $orderId = (int) $pdo->lastInsertId();
    $itemStatement = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, size, quantity, price)
         VALUES (:order_id, :product_id, :product_name, :size, :quantity, :price)'
    );
    $stockUpdateStatement = $pdo->prepare('UPDATE products SET stock = stock - :quantity WHERE id = :product_id');

    foreach ($freshCart as $item) {
        $itemStatement->execute([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'product_name' => $item['name'],
            'size' => $item['size'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
        ]);
        $stockUpdateStatement->execute([
            'quantity' => $item['quantity'],
            'product_id' => $item['product_id'],
        ]);
    }

    if ($user) {
        $profileStatement = $pdo->prepare(
            'UPDATE users
             SET name = :name, email = :email, phone = :phone, address = :address, city = :city, postal_code = :postal_code
             WHERE id = :id'
        );
        $profileStatement->execute([
            'name' => $customerName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postalCode,
            'id' => $user['id'],
        ]);

        $_SESSION['user']['name'] = $customerName;
        $_SESSION['user']['email'] = $email;
    }

    $pdo->commit();
    notify_admins_new_order([
        'id' => $orderId,
        'customer_name' => $customerName,
        'email' => $email,
        'phone' => $phone,
        'total' => $total,
    ]);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    set_flash('checkout_error', $exception->getMessage());
    redirect_to('../pages/checkout.php');
}

unset($_SESSION['cart']);
$_SESSION['order_success'] = $orderId;

redirect_to('../pages/checkout.php?success=1');