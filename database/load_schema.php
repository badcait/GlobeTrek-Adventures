<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$sql = file_get_contents(__DIR__ . '/schema.sql');
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS);
$connection->set_charset('utf8mb4');
$connection->multi_query($sql);

do {
    if ($result = $connection->store_result()) {
        $result->free();
    }
} while ($connection->more_results() && $connection->next_result());

echo "schema loaded\n";

