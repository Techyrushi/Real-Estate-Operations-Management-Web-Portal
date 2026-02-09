<?php
require 'config/db.php';
$stmt = $pdo->query("DESCRIBE banks");
echo "Table: banks\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
