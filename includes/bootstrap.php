<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/task_repository.php';
require_once __DIR__ . '/auth.php';

$pdo = null;
$dbError = null;

try {
    $pdo = get_db_connection();
} catch (PDOException $exception) {
    $dbError = $exception->getMessage();
}
