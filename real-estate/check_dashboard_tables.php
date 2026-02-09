<?php
require 'config/db.php';

function checkTable($pdo, $tableName) {
    try {
        echo "Table: $tableName\n";
        $stmt = $pdo->query("DESCRIBE $tableName");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  {$col['Field']} ({$col['Type']})\n";
        }
    } catch (PDOException $e) {
        echo "  Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

checkTable($pdo, 'customers');
?>
