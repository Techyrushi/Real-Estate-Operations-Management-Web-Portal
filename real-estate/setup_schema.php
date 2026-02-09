<?php
include 'config/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Projects Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        type ENUM('Residential', 'Commercial', 'Mixed') NOT NULL,
        address TEXT,
        location_details TEXT,
        ready_reckoner_rate_res DECIMAL(15,2) DEFAULT 0.00,
        ready_reckoner_rate_com DECIMAL(15,2) DEFAULT 0.00,
        carpet_area DECIMAL(10,2),
        sellable_area DECIMAL(10,2),
        num_units INT DEFAULT 0,
        rera_reg VARCHAR(50),
        status ENUM('Planning', 'Ongoing', 'Completed') DEFAULT 'Planning',
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Projects table configured.<br>";

    // --- Units Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        flat_no VARCHAR(50) NOT NULL,
        floor INT,
        configuration VARCHAR(50),
        area DECIMAL(10,2),
        rate DECIMAL(15,2),
        status ENUM('Available', 'Booked', 'Sold') DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
    )");
    echo "Units table configured.<br>";

    // --- Partners Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        mobile VARCHAR(20),
        percentage_share DECIMAL(5,2) DEFAULT 0.00,
        opening_capital DECIMAL(15,2) DEFAULT 0.00,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Partners table configured.<br>";

    // --- Partner Ledger Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS partner_ledger (
        id INT AUTO_INCREMENT PRIMARY KEY,
        partner_id INT NOT NULL,
        transaction_date DATE NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        mode VARCHAR(50),
        remarks TEXT,
        type ENUM('Credit', 'Debit') NOT NULL COMMENT 'Credit: Investment, Debit: Withdrawal',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
    )");
    echo "Partner Ledger table configured.<br>";

    // --- Banks Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS banks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT,
        bank_name VARCHAR(255) NOT NULL,
        branch VARCHAR(255),
        account_number VARCHAR(100),
        ifsc_code VARCHAR(50),
        account_type ENUM('RERA Escrow', 'Construction', 'Collection') DEFAULT 'Construction',
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
    )");
    echo "Banks table configured.<br>";

    // --- Materials Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(255) NOT NULL,
        unit_measure VARCHAR(50),
        standard_rate DECIMAL(15,2),
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Materials table configured.<br>";

    // --- Vendors Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS vendors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        gst_number VARCHAR(50),
        contact_details TEXT,
        bank_details TEXT,
        material_category VARCHAR(255),
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Vendors table configured.<br>";

    // --- Expenses Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT,
        vendor_id INT,
        material_id INT,
        expense_date DATE NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        gst_amount DECIMAL(15,2) DEFAULT 0.00,
        payment_mode VARCHAR(50),
        bank_id INT,
        reference_no VARCHAR(100),
        remarks TEXT,
        attachment VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE SET NULL,
        FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE SET NULL,
        FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE SET NULL
    )");
    echo "Expenses table configured.<br>";

    // --- Customers Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        mobile VARCHAR(20),
        email VARCHAR(255),
        address TEXT,
        pan VARCHAR(50),
        project_id INT,
        unit_id INT,
        area_carpet DECIMAL(10,2),
        area_sellable DECIMAL(10,2),
        rate_per_sqft DECIMAL(15,2),
        total_deal_amount DECIMAL(15,2),
        agreement_value DECIMAL(15,2),
        booking_date DATE,
        status ENUM('Booked', 'Cancelled', 'Completed') DEFAULT 'Booked',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL
    )");
    echo "Customers table configured.<br>";

    // --- Customer Payments Table ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS customer_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        payment_date DATE NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        payment_mode VARCHAR(50),
        bank_id INT,
        receipt_no VARCHAR(100),
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
        FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE SET NULL
    )");
    echo "Customer Payments table configured.<br>";

    // --- Add Permissions ---
    $permissions = [
        'manage_projects', 'manage_partners', 'manage_banks', 'manage_materials',
        'manage_vendors', 'manage_expenses', 'manage_customers', 'manage_reports'
    ];
    
    foreach ($permissions as $slug) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM permissions WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO permissions (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([ucwords(str_replace('_', ' ', $slug)), $slug, 'Access to ' . str_replace('_', ' ', $slug)]);
        }
    }
    echo "Permissions updated.<br>";
    
    // Assign new permissions to Admin (role_id = 1 usually)
    // First get admin role id
    $stmt = $pdo->query("SELECT id FROM roles WHERE name = 'Admin'");
    $admin_role_id = $stmt->fetchColumn();
    
    if ($admin_role_id) {
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE slug IN ('" . implode("','", $permissions) . "')");
        $stmt->execute();
        $perm_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($perm_ids as $perm_id) {
            $check = $pdo->prepare("SELECT COUNT(*) FROM role_permissions WHERE role_id = ? AND permission_id = ?");
            $check->execute([$admin_role_id, $perm_id]);
            if ($check->fetchColumn() == 0) {
                $ins = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                $ins->execute([$admin_role_id, $perm_id]);
            }
        }
        echo "Admin permissions updated.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>