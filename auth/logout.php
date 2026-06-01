<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['user']);
session_regenerate_id(true);

redirect_to('../index.php');
