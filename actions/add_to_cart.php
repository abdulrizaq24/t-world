<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../pages/shop.php');
}

require_valid_csrf('../pages/shop.php', 'cart_error');

$productId = (int) ($_POST['product_id'] ?? 0);
$size = $_POST['size'] ?? 'M';
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$product = get_product($productId);

if (!$product) {
    redirect_to('../pages/shop.php');
}

if ((int) $product['stock'] <= 0) {
    set_flash('cart_error', 'This product is currently out of stock.');
    redirect_to('../pages/product_details.php?id=' . $productId);
}

if (!in_array($size, allowed_sizes(), true)) {
    $size = 'M';
}

$quantity = min($quantity, max(1, (int) $product['stock']));
$cartKey = $productId . ':' . $size;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$cartKey])) {
    $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$cartKey] = [
        'product_id' => $productId,
        'name' => $product['name'],
        'price' => (float) $product['price'],
        'image_url' => $product['image_url'],
        'size' => $size,
        'quantity' => $quantity,
    ];
}

redirect_to('../pages/cart.php');