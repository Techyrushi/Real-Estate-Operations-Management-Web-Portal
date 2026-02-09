<?php
include 'config/db.php';

try {
    // Add columns to units table if they don't exist
    $columns = [
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
            // Column likely exists
            echo "Column $col likely exists or error: " . $e->getMessage() . "<br>";
        }
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
