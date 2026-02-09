<?php
include 'config/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create Expenses Table
    echo "<h3>Creating Expenses Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS expenses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        vendor_id INT,
        material_id INT,
        expense_date DATE NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        gst_amount DECIMAL(15,2) DEFAULT 0.00,
        payment_mode ENUM('Cash', 'Bank Transfer', 'Cheque', 'UPI', 'Other') DEFAULT 'Bank Transfer',
        bank_id INT,
        reference_no VARCHAR(100),
        remarks TEXT,
        invoice_file VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
        FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE SET NULL,
        FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Expenses table created/verified.<br>";

    // 2. Create Customers Table
    echo "<h3>Creating Customers Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        email VARCHAR(255),
        address TEXT,
        pan VARCHAR(20),
        project_id INT NOT NULL,
        unit_id INT,
        carpet_area DECIMAL(10,2) DEFAULT 0.00,
        sellable_area DECIMAL(10,2) DEFAULT 0.00,
        rate_per_sqft DECIMAL(10,2) DEFAULT 0.00,
        total_deal_amount DECIMAL(15,2) DEFAULT 0.00,
        agreement_value DECIMAL(15,2) DEFAULT 0.00,
        booking_date DATE,
        status ENUM('Booked', 'Agreement Done', 'Cancelled', 'Completed') DEFAULT 'Booked',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Customers table created/verified.<br>";

    // 3. Create Customer Payments Table
    echo "<h3>Creating Customer Payments Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS customer_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        payment_date DATE NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        payment_mode ENUM('Cash', 'Bank Transfer', 'Cheque', 'UPI', 'Other') DEFAULT 'Bank Transfer',
        bank_id INT,
        receipt_no VARCHAR(100),
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
        FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Customer Payments table created/verified.<br>";

    echo "<h3>Schema Update Completed Successfully!</h3>";

} catch (PDOException $e) {
    echo "<h3>Error: " . $e->getMessage() . "</h3>";
}
?>
