<?php
include 'config/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add 'type' column to partner_ledger
    echo "<h3>Updating Partner Ledger Table...</h3>";
    try {
        $pdo->exec("ALTER TABLE partner_ledger ADD COLUMN type ENUM('Credit', 'Debit') DEFAULT 'Credit' AFTER amount");
        echo "Added column 'type' to partner_ledger.<br>";
    } catch (PDOException $e) {
        echo "Column 'type' likely exists in partner_ledger.<br>";
    }

    echo "<h3>Schema Update Completed Successfully!</h3>";

} catch (PDOException $e) {
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
}
?>
