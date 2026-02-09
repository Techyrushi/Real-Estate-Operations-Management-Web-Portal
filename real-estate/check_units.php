<?php
include 'config/db.php';
try {
    $stmt = $pdo->query("DESCRIBE units");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (PDOException $e) {
    echo "Table 'units' might not exist: " . $e->getMessage();
}
?>
