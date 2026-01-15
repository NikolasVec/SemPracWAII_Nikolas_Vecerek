<?php
// Quick DB check script - one-off diagnostic
require_once __DIR__ . '/../App/Configuration.php';
require_once __DIR__ . '/../Framework/DB/Connection.php';

use Framework\DB\Connection;

try {
    $conn = Connection::getInstance();
    $stmt = $conn->query('SELECT COUNT(*) AS c FROM Stanovisko');
    $count = $stmt->fetchColumn();
    echo "COUNT=" . intval($count) . PHP_EOL;

    if ($count > 0) {
        $rows = $conn->query('SELECT * FROM Stanovisko LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}

