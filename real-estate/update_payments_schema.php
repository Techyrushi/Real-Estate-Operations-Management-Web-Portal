<?php
require 'config/db.php';

try {
    // Add bank_id
    $pdo->exec("ALTER TABLE payments ADD COLUMN bank_id INT(11) NULL AFTER payment_method");
    echo "Added bank_id column.\n";
} catch (PDOException $e) {
    echo "bank_id column might already exist or error: " . $e->getMessage() . "\n";
}

try {
    // Add receipt_no
    $pdo->exec("ALTER TABLE payments ADD COLUMN receipt_no VARCHAR(100) NULL AFTER bank_id");
    echo "Added receipt_no column.\n";
} catch (PDOException $e) {
    echo "receipt_no column might already exist or error: " . $e->getMessage() . "\n";
}

try {
    // Add remarks
    $pdo->exec("ALTER TABLE payments ADD COLUMN remarks TEXT NULL AFTER receipt_no");
    echo "Added remarks column.\n";
} catch (PDOException $e) {
    echo "remarks column might already exist or error: " . $e->getMessage() . "\n";
}

try {
    // Add updated_at
    $pdo->exec("ALTER TABLE payments ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
    echo "Added updated_at column.\n";
} catch (PDOException $e) {
    echo "updated_at column might already exist or error: " . $e->getMessage() . "\n";
}

echo "Payments table schema update completed.\n";
?>
