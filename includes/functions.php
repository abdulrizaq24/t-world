<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function h(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function money(float $value): string
{
    return '$' . number_format($value, 2);
}

function base_path(string $path = ''): string
{
    $basePath = $GLOBALS['basePath'] ?? '';
    return $basePath . ltrim($path, '/');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_admin(): array
{
    $user = current_user();

    if (!$user || ($user['role'] ?? '') !== 'admin') {
        redirect_to('../auth/login.php');
    }

    return $user;
}

function allowed_sizes(): array
{
    return ['S', 'M', 'L', 'XL'];
}

function get_products(?string $category = null, string $search = ''): array
{
    global $pdo;

    $sql = 'SELECT * FROM products WHERE is_active = 1';
    $params = [];

    if ($category && $category !== 'all') {
        $sql .= ' AND category = :category';
        $params['category'] = $category;
    }

    if ($search !== '') {
        $sql .= ' AND (name LIKE :search OR category LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY created_at DESC';
    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function get_product(int $id): ?array
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM products WHERE id = :id AND is_active = 1');
    $statement->execute(['id' => $id]);
    $product = $statement->fetch();

    return $product ?: null;
}

function get_admin_product(int $id): ?array
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    $product = $statement->fetch();

    return $product ?: null;
}

function product_categories(): array
{
    return [
        'plain' => 'Plain',
        'oversized' => 'Oversized',
        'graphic' => 'Graphic',
        'new' => 'New Arrivals',
    ];
}

function cart_items(): array
{
    $cart = $_SESSION['cart'] ?? [];

    foreach ($cart as $cartKey => $item) {
        $product = get_product((int) ($item['product_id'] ?? 0));

        if (!$product) {
            unset($cart[$cartKey]);
            continue;
        }

        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $stock = max(0, (int) $product['stock']);

        if ($stock === 0) {
            unset($cart[$cartKey]);
            continue;
        }

        $cart[$cartKey] = [
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'image_url' => $product['image_url'],
            'size' => in_array($item['size'] ?? '', allowed_sizes(), true) ? $item['size'] : 'M',
            'quantity' => min($quantity, $stock),
            'stock' => $stock,
        ];
    }

    $_SESSION['cart'] = $cart;

    return $cart;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function flash(string $type): ?string
{
    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $message = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);

    return $message;
}

function cart_subtotal(): float
{
    return array_reduce(cart_items(), function (float $sum, array $item): float {
        return $sum + ((float) $item['price'] * (int) $item['quantity']);
    }, 0.0);
}

function cart_shipping(): float
{
    return cart_subtotal() > 0 ? 5.00 : 0.00;
}

function cart_total(): float
{
    return cart_subtotal() + cart_shipping();
}

function redirect_to(string $path): never
{
    header('Location: ' . $path);
    exit;
}
