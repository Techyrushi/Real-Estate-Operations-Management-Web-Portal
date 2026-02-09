<?php
include 'config/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Update Projects Table
    echo "<h3>Updating Projects Table...</h3>";
    $project_columns = [
        'type' => "ADD COLUMN type ENUM('Residential', 'Commercial', 'Mixed') DEFAULT 'Residential'",
        'location_details' => "ADD COLUMN location_details TEXT",
        'rera_reg' => "ADD COLUMN rera_reg VARCHAR(100)",
        'ready_reckoner_rate_res' => "ADD COLUMN ready_reckoner_rate_res DECIMAL(10,2) DEFAULT 0.00",
        'ready_reckoner_rate_com' => "ADD COLUMN ready_reckoner_rate_com DECIMAL(10,2) DEFAULT 0.00",
        'carpet_area' => "ADD COLUMN carpet_area DECIMAL(10,2) DEFAULT 0.00",
        'sellable_area' => "ADD COLUMN sellable_area DECIMAL(10,2) DEFAULT 0.00",
        'num_units' => "ADD COLUMN num_units INT DEFAULT 0"
    ];

    foreach ($project_columns as $col => $sql) {
        try {
            $pdo->exec("ALTER TABLE projects $sql");
            echo "Added column $col to projects.<br>";
        } catch (PDOException $e) {
            echo "Column $col likely exists.<br>";
        }
    }

    // 2. Create Partners Table
    echo "<h3>Creating Partners Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        mobile VARCHAR(20),
        share_percentage DECIMAL(5,2) DEFAULT 0.00,
        opening_capital DECIMAL(15,2) DEFAULT 0.00,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Partners table created/verified.<br>";

    // 3. Create Partner Ledger Table
    echo "<h3>Creating Partner Ledger Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS partner_ledger (
        id INT AUTO_INCREMENT PRIMARY KEY,
        partner_id INT NOT NULL,
        transaction_date DATE NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        mode VARCHAR(50) NOT NULL,
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Partner Ledger table created/verified.<br>";

    // 4. Create Banks Table
    echo "<h3>Creating Banks Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS banks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT,
        bank_name VARCHAR(255) NOT NULL,
        branch VARCHAR(255),
        account_no VARCHAR(100),
        ifsc_code VARCHAR(50),
        account_type ENUM('RERA Escrow', 'Construction', 'Collection', 'Other') DEFAULT 'Other',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Banks table created/verified.<br>";

    // 5. Create Materials Table
    echo "<h3>Creating Materials Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        uom VARCHAR(50),
        standard_rate DECIMAL(10,2) DEFAULT 0.00,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Materials table created/verified.<br>";

    // 6. Create Vendors Table
    echo "<h3>Creating Vendors Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS vendors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        gst_no VARCHAR(50),
        contact_details TEXT,
        bank_details TEXT,
        service_provided TEXT,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Vendors table created/verified.<br>";

    echo "<h3>Schema Update Completed Successfully!</h3>";

} catch (PDOException $e) {
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
}
?>
