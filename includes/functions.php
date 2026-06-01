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

function require_customer(): array
{
    $user = current_user();

    if (!$user) {
        redirect_to('../auth/login.php');
    }

    return $user;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '" />';
}

function csrf_token_is_valid(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function require_valid_csrf(string $redirectPath, string $flashType = 'admin_error'): void
{
    if (!csrf_token_is_valid($_POST['csrf_token'] ?? null)) {
        set_flash($flashType, 'Your session security token expired. Please try again.');
        redirect_to($redirectPath);
    }
}

function allowed_sizes(): array
{
    return ['S', 'M', 'L', 'XL'];
}

function email_is_valid(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function phone_is_valid(string $phone): bool
{
    return preg_match('/^[0-9+()\-\s]{7,40}$/', $phone) === 1;
}

function password_validation_error(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must include at least one uppercase letter.';
    }

    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must include at least one lowercase letter.';
    }

    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must include at least one number.';
    }

    return null;
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

function order_statuses(): array
{
    return [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'cancelled' => 'Cancelled',
    ];
}

function get_order(int $id): ?array
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
    $statement->execute(['id' => $id]);
    $order = $statement->fetch();

    return $order ?: null;
}

function get_order_items(int $orderId): array
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC');
    $statement->execute(['order_id' => $orderId]);

    return $statement->fetchAll();
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

function cart_count(): int
{
    return array_reduce(cart_items(), function (int $sum, array $item): int {
        return $sum + (int) $item['quantity'];
    }, 0);
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

function pagination_links(string $basePath, array $query, int $currentPage, int $totalPages): string
{
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<nav class="pagination" aria-label="Pagination">';

    for ($page = 1; $page <= $totalPages; $page++) {
        $query['page'] = $page;
        $href = $basePath . '?' . http_build_query($query);
        $activeClass = $page === $currentPage ? ' class="active"' : '';
        $html .= '<a' . $activeClass . ' href="' . h($href) . '">' . h((string) $page) . '</a>';
    }

    $html .= '</nav>';

    return $html;
}

function redirect_to(string $path): never
{
    header('Location: ' . $path);
    exit;
}


