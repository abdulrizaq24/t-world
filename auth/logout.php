<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['user']);
session_regenerate_id(true);
set_flash('success', 'You have been logged out successfully.');

redirect_to('../auth/login.php');