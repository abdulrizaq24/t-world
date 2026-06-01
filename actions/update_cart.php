<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['quantities'] ?? [] as $cartKey => $quantity) {
        if (!isset($_SESSION['cart'][$cartKey])) {
            continue;
        }

        $product = get_product((int) $_SESSION['cart'][$cartKey]['product_id']);

        if (!$product) {
            unset($_SESSION['cart'][$cartKey]);
            continue;
        }

        $_SESSION['cart'][$cartKey]['quantity'] = min(
            max(1, (int) $quantity),
            max(1, (int) $product['stock'])
        );
    }
}

redirect_to('../pages/cart.php');
