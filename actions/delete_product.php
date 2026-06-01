<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../admin/dashboard.php#products');
}

require_valid_csrf('../admin/dashboard.php#products');

global $pdo;

$id = (int) ($_POST['id'] ?? 0);
$product = get_admin_product($id);

if (!$product) {
    set_flash('admin_error', 'Product was not found.');
    redirect_to('../admin/dashboard.php#products');
}

$usageStatement = $pdo->prepare('SELECT COUNT(*) FROM order_items WHERE product_id = :product_id');
$usageStatement->execute(['product_id' => $id]);
$orderItemCount = (int) $usageStatement->fetchColumn();

if ($orderItemCount > 0) {
    set_flash('admin_error', 'This product has order history, so it cannot be deleted. Hide it instead.');
    redirect_to('../admin/dashboard.php#products');
}

$deleteStatement = $pdo->prepare('DELETE FROM products WHERE id = :id');
$deleteStatement->execute(['id' => $id]);

$imagePath = (string) ($product['image_url'] ?? '');
$uploadPrefix = 'uploads/products/';

if (str_starts_with($imagePath, $uploadPrefix)) {
    $fullImagePath = realpath(__DIR__ . '/../' . $imagePath);
    $uploadDir = realpath(__DIR__ . '/../uploads/products');

    if ($fullImagePath && $uploadDir && str_starts_with($fullImagePath, $uploadDir) && is_file($fullImagePath)) {
        unlink($fullImagePath);
    }
}

set_flash('admin_success', 'Product deleted successfully.');
redirect_to('../admin/dashboard.php#products');