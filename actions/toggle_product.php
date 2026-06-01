<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf('../admin/dashboard.php#products');

    global $pdo;

    $id = (int) ($_POST['id'] ?? 0);
    $product = get_admin_product($id);

    if ($product) {
        $statement = $pdo->prepare('UPDATE products SET is_active = :is_active WHERE id = :id');
        $statement->execute([
            'id' => $id,
            'is_active' => (int) $product['is_active'] === 1 ? 0 : 1,
        ]);

        set_flash('admin_success', 'Product status updated.');
    }
}

redirect_to('../admin/dashboard.php#products');