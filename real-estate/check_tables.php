<?php
include 'config/db.php';

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current Tables:\n";
    foreach ($tables as $table) {
        echo "- " . $table . "\n";
        
        // Show columns for context
        $stmt2 = $pdo->query("DESCRIBE $table");
        $columns = $stmt2->fetchAll(PDO::FETCH_COLUMN);
        echo "  Columns: " . implode(", ", $columns) . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>