<?php
include 'config/db.php';

try {
    // Optional hard reset to clear old units/bookings data
    if (isset($_GET['reset_units']) && $_GET['reset_units'] === '1') {
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            foreach (['payments', 'bookings', 'units'] as $tbl) {
                try {
                    $pdo->exec("TRUNCATE TABLE $tbl");
                    echo "Truncated table $tbl.<br>";
                } catch (PDOException $e) {
                    echo "Skip truncate for $tbl: " . $e->getMessage() . "<br>";
                }
            }

            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            echo "Reset of units/bookings/payments data completed.<br>";
        } catch (PDOException $e) {
            echo "Error during reset: " . $e->getMessage() . "<br>";
        }
    }

    // Add/ensure columns on units table for flat-wise configuration
    $columns = [
        'flat_no' => "ADD COLUMN flat_no VARCHAR(50)",
        'floor' => "ADD COLUMN floor INT",
        'configuration' => "ADD COLUMN configuration VARCHAR(50)",
        'area' => "ADD COLUMN area DECIMAL(10,2) DEFAULT 0.00",
        'rate' => "ADD COLUMN rate DECIMAL(15,2) DEFAULT 0.00",
        'status' => "ADD COLUMN status ENUM('Available', 'Booked', 'Sold') DEFAULT 'Available'",
        'bedrooms' => "ADD COLUMN bedrooms INT DEFAULT 0",
        'bathrooms' => "ADD COLUMN bathrooms INT DEFAULT 0",
        'property_type' => "ADD COLUMN property_type VARCHAR(50) DEFAULT 'Apartment'",
        'description' => "ADD COLUMN description TEXT",
        'image' => "ADD COLUMN image VARCHAR(255)"
    ];

    foreach ($columns as $col => $sql_part) {
        try {
            $pdo->exec("ALTER TABLE units $sql_part");
            echo "Added column $col to units table.<br>";
        } catch (PDOException $e) {
            echo "Column $col likely exists or error: " . $e->getMessage() . "<br>";
        }
    }

    // Migrate data from old schema columns if they exist
    try {
        $pdo->exec("UPDATE units SET flat_no = unit_number WHERE flat_no IS NULL OR flat_no = ''");
        echo "Migrated unit_number -> flat_no.<br>";
    } catch (PDOException $e) {
        echo "Skip unit_number migration: " . $e->getMessage() . "<br>";
    }

    try {
        $pdo->exec("UPDATE units SET configuration = type WHERE configuration IS NULL OR configuration = ''");
        echo "Migrated type -> configuration.<br>";
    } catch (PDOException $e) {
        echo "Skip type migration: " . $e->getMessage() . "<br>";
    }

    try {
        $pdo->exec("UPDATE units SET rate = price WHERE rate IS NULL OR rate = 0");
        echo "Migrated price -> rate.<br>";
    } catch (PDOException $e) {
        echo "Skip price migration: " . $e->getMessage() . "<br>";
    }
    
    // Also add city/state to projects if not present, for location filtering
    $proj_columns = [
        'city' => "ADD COLUMN city VARCHAR(100)",
        'state' => "ADD COLUMN state VARCHAR(100)"
    ];

    foreach ($proj_columns as $col => $sql_part) {
        try {
            $pdo->exec("ALTER TABLE projects $sql_part");
            echo "Added column $col to projects table.<br>";
        } catch (PDOException $e) {
            echo "Column $col likely exists or error: " . $e->getMessage() . "<br>";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
