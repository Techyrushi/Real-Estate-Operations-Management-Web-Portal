<?php
require 'config/db.php';

try {
    // Add agreement_value
    $pdo->exec("ALTER TABLE bookings ADD COLUMN agreement_value DECIMAL(15,2) NULL AFTER total_price");
    echo "Added agreement_value column.\n";
} catch (PDOException $e) {
    echo "agreement_value column might already exist.\n";
}

try {
    // Add rate_per_sqft
    $pdo->exec("ALTER TABLE bookings ADD COLUMN rate_per_sqft DECIMAL(10,2) NULL AFTER total_price");
    echo "Added rate_per_sqft column.\n";
} catch (PDOException $e) {
    echo "rate_per_sqft column might already exist.\n";
}

echo "Bookings table schema update completed.\n";
?>
