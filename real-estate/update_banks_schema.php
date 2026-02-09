<?php
require 'config/db.php';

try {
    // Add opening_balance
    $pdo->exec("ALTER TABLE banks ADD COLUMN opening_balance DECIMAL(15,2) DEFAULT 0.00 AFTER account_type");
    echo "Added opening_balance column to banks.\n";
} catch (PDOException $e) {
    echo "opening_balance column might already exist in banks.\n";
}

echo "Banks schema update completed.\n";
?>
