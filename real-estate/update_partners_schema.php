<?php
require 'config/db.php';

try {
    $columns = [
        'email' => "VARCHAR(100) AFTER name",
        'mobile' => "VARCHAR(20) AFTER email",
        'percentage_share' => "DECIMAL(5,2) DEFAULT 0.00 AFTER mobile",
        'opening_capital' => "DECIMAL(15,2) DEFAULT 0.00 AFTER percentage_share",
        'status' => "ENUM('Active', 'Inactive') DEFAULT 'Active' AFTER capital_contribution"
    ];

    foreach ($columns as $col => $def) {
        try {
            $pdo->exec("ALTER TABLE partners ADD COLUMN $col $def");
            echo "Added $col to partners.\n";
        } catch (PDOException $e) {
            echo "Column $col might already exist in partners.\n";
        }
    }

    echo "Partners schema update completed.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
