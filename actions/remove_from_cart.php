<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf('../pages/cart.php', 'cart_error');
    $cartKey = $_POST['cart_key'] ?? '';
    unset($_SESSION['cart'][$cartKey]);
}

redirect_to('../pages/cart.php');
