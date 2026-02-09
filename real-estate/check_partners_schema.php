<?php
require 'config/db.php';
$stmt = $pdo->query("DESCRIBE partners");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}
?>
