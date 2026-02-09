<?php
include 'config/db.php';

function getTableColumns($pdo, $table) {
    $stmt = $pdo->prepare("DESCRIBE $table");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

try {
    echo "Users table columns:\n";
    $columns = getTableColumns($pdo, 'users');
    print_r($columns);
    
    echo "\nTables in database:\n";
    $stmt = $pdo->query("SHOW TABLES");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>