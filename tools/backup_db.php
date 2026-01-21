<?php
use Framework\DB\Connection;
// explanation: Simple standalone DB backup script that uses the project's Connection singleton to export all tables
// into a timestamped .sql file under sql/backups/. It boots the project's ClassLoader so Framework\DB\Connection
// can be used without modifying other project files.

// Bootstrap the project autoloader if available.
$loaderPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR . 'ClassLoader.php';
if (file_exists($loaderPath)) {
    require_once $loaderPath;
    // ClassLoader in this project registers itself on include; if not, attempt a simple autoload fallback.
}

// Minimal fallback autoloader for core classes (keeps the script resilient if ClassLoader isn't wired exactly the same)
spl_autoload_register(function ($class) {
    $base = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    $file = $base . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Create backup directory
$backupDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'backups';
if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0775, true)) {
        fwrite(STDERR, "Failed to create backup directory: $backupDir\n");
        exit(1);
    }
}

$timestamp = date('Ymd_His');
$backupFile = $backupDir . DIRECTORY_SEPARATOR . "backup_$timestamp.sql";

try {
    $conn = Connection::getInstance();

    // List tables
    $tables = [];
    $stmt = $conn->query('SHOW TABLES');
    $rows = $stmt->fetchAll(\PDO::FETCH_NUM);
    foreach ($rows as $r) {
        $tables[] = $r[0];
    }

    // Open file for streaming
    $fh = fopen($backupFile, 'wb');
    if ($fh === false) {
        fwrite(STDERR, "Failed to open backup file for writing: $backupFile\n");
        exit(1);
    }

    fwrite($fh, "-- Backup created: " . date('c') . "\n-- Database: " . \App\Configuration::DB_NAME . "\n\n");

    foreach ($tables as $table) {
        // Get CREATE TABLE
        $row = $conn->query("SHOW CREATE TABLE `" . $table . "`")->fetch(\PDO::FETCH_ASSOC);
        if (isset($row['Create Table'])) {
            fwrite($fh, "-- ----------------------------\n");
            fwrite($fh, "-- Table structure for `$table`\n");
            fwrite($fh, "-- ----------------------------\n");
            fwrite($fh, $row['Create Table'] . ";\n\n");
        }

        // Export data in batches
        fwrite($fh, "-- ----------------------------\n");
        fwrite($fh, "-- Records for `$table`\n");
        fwrite($fh, "-- ----------------------------\n");

        $countRow = $conn->query("SELECT COUNT(*) as c FROM `" . $table . "`")->fetch(\PDO::FETCH_ASSOC);
        $count = (int)($countRow['c'] ?? 0);
        if ($count === 0) {
            fwrite($fh, "\n");
            continue;
        }

        $batchSize = 200;
        for ($offset = 0; $offset < $count; $offset += $batchSize) {
            $stmt = $conn->query("SELECT * FROM `" . $table . "` LIMIT $offset, $batchSize");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$rows) continue;

            $columns = array_keys($rows[0]);
            $colsEscaped = array_map(function ($c) { return "`" . str_replace('`', '\\`', $c) . "`"; }, $columns);
            fwrite($fh, "INSERT INTO `" . $table . "` (" . implode(', ', $colsEscaped) . ") VALUES\n");

            $values = [];
            foreach ($rows as $r) {
                $vals = [];
                foreach ($columns as $c) {
                    if (is_null($r[$c])) {
                        $vals[] = 'NULL';
                    } else {
                        // Escape using PDO quote if available
                        $vals[] = $conn->quote((string)$r[$c]);
                    }
                }
                $values[] = '(' . implode(', ', $vals) . ')';
            }
            fwrite($fh, implode(",\n", $values) . ";\n\n");
        }
    }

    fclose($fh);

    fwrite(STDOUT, "Backup saved to: $backupFile\n");
    exit(0);
} catch (\Exception $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(2);
}
