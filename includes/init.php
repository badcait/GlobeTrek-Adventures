<?php
declare(strict_types=1);

$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '';

if (session_status() === PHP_SESSION_NONE) {
    $tempRoot = getenv('TEMP') ?: sys_get_temp_dir();
    $sessionPath = rtrim($tempRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'globetrek_sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0777, true);
    }
    session_save_path($sessionPath);
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
