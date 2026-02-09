<?php
require 'config/db.php';

try {
    // Add bank_id to partner_ledger
    $pdo->exec("ALTER TABLE partner_ledger ADD COLUMN bank_id INT NULL AFTER mode");
    echo "Added bank_id column to partner_ledger.\n";
} catch (PDOException $e) {
    echo "bank_id column might already exist in partner_ledger.\n";
}

try {
    // Add receipt_no to partner_ledger
    $pdo->exec("ALTER TABLE partner_ledger ADD COLUMN receipt_no VARCHAR(100) NULL AFTER bank_id");
    echo "Added receipt_no column to partner_ledger.\n";
} catch (PDOException $e) {
    echo "receipt_no column might already exist in partner_ledger.\n";
}

echo "Partner Ledger schema update completed.\n";
?>
