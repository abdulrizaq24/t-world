<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../admin/orders.php');
}

$returnId = (int) ($_POST['return_id'] ?? 0);
require_valid_csrf('../admin/return_details.php?id=' . $returnId);

global $pdo;

$status = $_POST['status'] ?? '';
$adminNote = trim($_POST['admin_note'] ?? '');

if (!$returnId || !array_key_exists($status, return_statuses())) {
    set_flash('admin_error', 'Invalid return status.');
    redirect_to('../admin/returns.php');
}

$statement = $pdo->prepare('UPDATE return_requests SET status = :status, admin_note = :admin_note WHERE id = :id');
$statement->execute([
    'id' => $returnId,
    'status' => $status,
    'admin_note' => $adminNote !== '' ? $adminNote : null,
]);

set_flash('admin_success', 'Return request updated.');
redirect_to('../admin/return_details.php?id=' . $returnId);