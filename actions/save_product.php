<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../admin/dashboard.php#products');
}

require_valid_csrf('../admin/dashboard.php#products');

global $pdo;

function product_form_redirect(int $id): never
{
    redirect_to('../admin/product_form.php' . ($id > 0 ? '?id=' . $id : ''));
}

function has_product_image_upload(): bool
{
    return isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE;
}

function delete_uploaded_product_image(string $imagePath): void
{
    $uploadPrefix = 'uploads/products/';

    if (!str_starts_with($imagePath, $uploadPrefix)) {
        return;
    }

    $fullImagePath = realpath(__DIR__ . '/../' . $imagePath);
    $uploadDir = realpath(__DIR__ . '/../uploads/products');

    if ($fullImagePath && $uploadDir && str_starts_with($fullImagePath, $uploadDir) && is_file($fullImagePath)) {
        unlink($fullImagePath);
    }
}

function uploaded_product_image_path(int $id, string $currentPath): string
{
    if (!has_product_image_upload()) {
        return $currentPath;
    }

    $file = $_FILES['product_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        set_flash('admin_error', 'Image upload failed. Try again.');
        product_form_redirect($id);
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        set_flash('admin_error', 'Image must be 2MB or smaller.');
        product_form_redirect($id);
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!isset($allowedTypes[$mimeType])) {
        set_flash('admin_error', 'Only JPG, PNG, and WEBP images are allowed.');
        product_form_redirect($id);
    }

    $uploadDir = __DIR__ . '/../uploads/products';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $fileName = 'product-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowedTypes[$mimeType];
    $targetPath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        set_flash('admin_error', 'Could not save uploaded image.');
        product_form_redirect($id);
    }

    return 'uploads/products/' . $fileName;
}

$id = (int) ($_POST['id'] ?? 0);
$existingProduct = $id > 0 ? get_admin_product($id) : null;

if ($id > 0 && !$existingProduct) {
    set_flash('admin_error', 'Product was not found.');
    redirect_to('../admin/dashboard.php#products');
}

$name = trim($_POST['name'] ?? '');
$category = $_POST['category'] ?? '';
$description = trim($_POST['description'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$stock = max(0, (int) ($_POST['stock'] ?? 0));
$imageUrl = trim($_POST['image_url'] ?? '');
$isActive = isset($_POST['is_active']) ? 1 : 0;
$oldImageUrl = (string) ($existingProduct['image_url'] ?? '');
$imageWasUploaded = has_product_image_upload();
$imageUrl = uploaded_product_image_path($id, $imageUrl);

if (
    $name === ''
    || !array_key_exists($category, product_categories())
    || $description === ''
    || $price < 0
    || $imageUrl === ''
) {
    if ($imageWasUploaded) {
        delete_uploaded_product_image($imageUrl);
    }

    set_flash('admin_error', 'Please complete all product fields correctly, including an image.');
    product_form_redirect($id);
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

    if ($imageWasUploaded && $oldImageUrl !== $imageUrl) {
        delete_uploaded_product_image($oldImageUrl);
    }
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