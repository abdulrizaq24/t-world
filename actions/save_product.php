<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../admin/dashboard.php#products');
}

global $pdo;

$id = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$category = $_POST['category'] ?? '';
$description = trim($_POST['description'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$stock = max(0, (int) ($_POST['stock'] ?? 0));
$imageUrl = trim($_POST['image_url'] ?? '');
$isActive = isset($_POST['is_active']) ? 1 : 0;

if (
    $name === ''
    || !array_key_exists($category, product_categories())
    || $description === ''
    || $price < 0
    || $imageUrl === ''
) {
    set_flash('admin_error', 'Please complete all product fields correctly.');
    redirect_to('../admin/product_form.php' . ($id > 0 ? '?id=' . $id : ''));
}

if ($id > 0) {
    $statement = $pdo->prepare(
        'UPDATE products
         SET name = :name,
             category = :category,
             description = :description,
             price = :price,
             stock = :stock,
             image_url = :image_url,
             is_active = :is_active
         WHERE id = :id'
    );
    $statement->execute([
        'id' => $id,
        'name' => $name,
        'category' => $category,
        'description' => $description,
        'price' => $price,
        'stock' => $stock,
        'image_url' => $imageUrl,
        'is_active' => $isActive,
    ]);
} else {
    $statement = $pdo->prepare(
        'INSERT INTO products (name, category, description, price, stock, image_url, is_active)
         VALUES (:name, :category, :description, :price, :stock, :image_url, :is_active)'
    );
    $statement->execute([
        'name' => $name,
        'category' => $category,
        'description' => $description,
        'price' => $price,
        'stock' => $stock,
        'image_url' => $imageUrl,
        'is_active' => $isActive,
    ]);
}

set_flash('admin_success', 'Product saved successfully.');
redirect_to('../admin/dashboard.php#products');
