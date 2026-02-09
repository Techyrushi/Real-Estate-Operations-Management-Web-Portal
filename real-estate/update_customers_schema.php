<?php
require 'config/db.php';

try {
    // Add address
    $pdo->exec("ALTER TABLE customers ADD COLUMN address TEXT NULL AFTER phone");
    echo "Added address column.\n";
} catch (PDOException $e) {
    echo "address column might already exist.\n";
}

try {
    // Add pan
    $pdo->exec("ALTER TABLE customers ADD COLUMN pan VARCHAR(20) NULL AFTER address");
    echo "Added pan column.\n";
} catch (PDOException $e) {
    echo "pan column might already exist.\n";
}

echo "Customers table schema update completed.\n";
?>
