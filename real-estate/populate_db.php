<?php
include 'config/db.php';

try {
    echo "Starting DB population...<br>";

    // --- Schema Migration (Ensure columns exist) ---
    $columns_to_add = [
        'type' => "ENUM('Apartment', 'Villa', 'Office', 'Shop', 'Other') NOT NULL DEFAULT 'Apartment' AFTER unit_number",
        'bedrooms' => "INT DEFAULT 0 AFTER price",
        'bathrooms' => "DECIMAL(3, 1) DEFAULT 0 AFTER bedrooms",
        'area' => "DECIMAL(10, 2) DEFAULT 0 AFTER bathrooms",
        'description' => "TEXT AFTER area",
        'image' => "VARCHAR(255) AFTER description"
    ];

    foreach ($columns_to_add as $col => $def) {
        $stmt = $pdo->query("SHOW COLUMNS FROM units LIKE '$col'");
        if ($stmt->rowCount() == 0) {
            echo "Adding column $col to units...<br>";
            $pdo->exec("ALTER TABLE units ADD COLUMN $col $def");
        }
    }
    echo "Schema updated.<br>";

    // Disable FK checks to allow truncation
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $tables = [
        'payments', 'bookings', 'customers', 'units', 'projects', 
        'expenses', 'partners', 'partner_ledger', 'vendors', 'materials', 'banks'
    ];
    
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE $table");
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables truncated.<br>";

    // --- 1. Projects ---
    $projects = [
        ['Skyline Tower', 'Residential', 'Luxury apartments with city view, gym, and pool.'],
        ['Ocean Breeze', 'Residential', 'Beachfront villas with private access to the sea.'],
        ['Tech Hub', 'Commercial', 'Modern office spaces with high-speed internet and conference rooms.'],
        ['Green Valley', 'Mixed', 'Eco-friendly community with parks, shops, and apartments.'],
        ['Sunset Mall', 'Commercial', 'Shopping and entertainment center with cinema and food court.']
    ];

    $project_ids = [];
    $stmt = $pdo->prepare("INSERT INTO projects (name, type, description) VALUES (?, ?, ?)");
    foreach ($projects as $p) {
        $stmt->execute($p);
        $project_ids[] = $pdo->lastInsertId();
    }
    echo "Projects inserted.<br>";

    // --- 1.5 Banks (Linked to Projects) ---
    $banks_data = [
        ['HDFC Bank', 'HDFC0001234', '1234567890', 'Main Branch', 5000000, 'RERA Escrow'],
        ['SBI', 'SBIN0004567', '0987654321', 'City Center', 2500000, 'Construction'],
        ['ICICI Bank', 'ICIC0007890', '1122334455', 'Tech Park', 7500000, 'Collection']
    ];
    $bank_ids = [];
    $bank_stmt = $pdo->prepare("INSERT INTO banks (project_id, bank_name, ifsc_code, account_number, branch, opening_balance, account_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')");
    
    foreach ($project_ids as $pid) {
        // Assign a random bank to each project
        $b = $banks_data[array_rand($banks_data)];
        $bank_stmt->execute([$pid, $b[0], $b[1], $b[2], $b[3], $b[4], $b[5]]);
        $bank_ids[] = $pdo->lastInsertId();
    }
    // Add a few more random banks
    for ($i=0; $i<3; $i++) {
        $pid = $project_ids[array_rand($project_ids)];
        $b = $banks_data[array_rand($banks_data)];
        $bank_stmt->execute([$pid, $b[0], $b[1], rand(1000000000, 9999999999), $b[3], $b[4], $b[5]]);
        $bank_ids[] = $pdo->lastInsertId();
    }
    echo "Banks inserted.<br>";

    // --- 2. Units ---
    $units_data = [];
    $unit_stmt = $pdo->prepare("INSERT INTO units (project_id, unit_number, type, status, price, bedrooms, bathrooms, area, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($project_ids as $pid) {
        // Fetch project type to decide unit types
        $p_stmt = $pdo->query("SELECT type FROM projects WHERE id = $pid");
        $p_type = $p_stmt->fetchColumn();

        for ($i = 1; $i <= 8; $i++) {
            $unit_number = "U-" . $pid . "-" . str_pad($i, 3, '0', STR_PAD_LEFT);
            
            // Determine Unit Type
            $type = 'Apartment';
            if ($p_type == 'Residential') $type = ($i % 3 == 0) ? 'Villa' : 'Apartment';
            if ($p_type == 'Commercial') $type = ($i % 2 == 0) ? 'Office' : 'Shop';
            if ($p_type == 'Mixed') $type = ($i % 3 == 0) ? 'Shop' : 'Apartment';

            // Random status but ensure some are sold/reserved
            $rand = rand(1, 10);
            if ($rand <= 4) $status = 'Sold';
            elseif ($rand <= 6) $status = 'Reserved';
            else $status = 'Available';

            $price = rand(20, 150) * 100000; // 20L to 1.5Cr
            $bedrooms = rand(1, 4);
            $bathrooms = rand(1, 3);
            $area = rand(600, 3000);
            $description = "Spacious $type with modern amenities.";
            $image = "property-" . rand(1, 5) . ".jpg"; 

            $unit_stmt->execute([$pid, $unit_number, $type, $status, $price, $bedrooms, $bathrooms, $area, $description, $image]);
        }
    }
    echo "Units inserted.<br>";

    // --- 3. Customers ---
    $customers = [
        ['Rahul Sharma', 'rahul@example.com', '9876543210', '123, MG Road, Bangalore', 'ABCDE1234F'],
        ['Priya Patel', 'priya@example.com', '8765432109', '45, Park Street, Kolkata', 'FGHIJ5678K'],
        ['Amit Singh', 'amit@example.com', '7654321098', '78, Nehru Place, Delhi', 'KLMNO9012P'],
        ['Sneha Gupta', 'sneha@example.com', '6543210987', '90, Jubilee Hills, Hyderabad', 'PQRST3456U'],
        ['Vikram Malhotra', 'vikram@example.com', '5432109876', '12, Marine Drive, Mumbai', 'UVWXY7890Z']
    ];
    $customer_ids = [];
    $cust_stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address, pan) VALUES (?, ?, ?, ?, ?)");
    foreach ($customers as $c) {
        $cust_stmt->execute($c);
        $customer_ids[] = $pdo->lastInsertId();
    }
    echo "Customers inserted.<br>";

    // --- 4. Bookings & Payments ---
    $booking_stmt = $pdo->prepare("INSERT INTO bookings (unit_id, customer_id, total_price, agreement_value, rate_per_sqft, booking_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $payment_stmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, payment_date, payment_method, bank_id, receipt_no, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Get sold/reserved units
    $sold_units_stmt = $pdo->query("SELECT id, price, status, area FROM units WHERE status IN ('Sold', 'Reserved')");
    while ($unit = $sold_units_stmt->fetch(PDO::FETCH_ASSOC)) {
        $customer_id = $customer_ids[array_rand($customer_ids)];
        // Random date in last 6 months
        $booking_date = date('Y-m-d', strtotime("-" . rand(1, 180) . " days"));
        $status = ($unit['status'] == 'Sold') ? 'Confirmed' : 'Pending';
        
        $agreement_value = $unit['price'];
        $rate_per_sqft = $unit['area'] > 0 ? $unit['price'] / $unit['area'] : 0;

        $booking_stmt->execute([$unit['id'], $customer_id, $unit['price'], $agreement_value, $rate_per_sqft, $booking_date, $status]);
        $booking_id = $pdo->lastInsertId();

        // Payments
        if ($status == 'Confirmed') {
            $bank_id = $bank_ids[array_rand($bank_ids)];
            
            // Initial Booking Amount
            $payment_stmt->execute([$booking_id, $unit['price'] * 0.1, $booking_date, 'Bank Transfer', $bank_id, 'REC-'.rand(1000,9999), 'Booking Amount']);
            
            // Second Payment (maybe a month later)
            $payment_date_2 = date('Y-m-d', strtotime($booking_date . " + 30 days"));
            if ($payment_date_2 < date('Y-m-d')) {
                $payment_stmt->execute([$booking_id, $unit['price'] * 0.4, $payment_date_2, 'Cheque', $bank_id, 'REC-'.rand(1000,9999), 'First Installment']);
            }
        } else {
            $bank_id = $bank_ids[array_rand($bank_ids)];
            // Token Amount
            $payment_stmt->execute([$booking_id, 50000, $booking_date, 'UPI', $bank_id, 'REC-'.rand(1000,9999), 'Token Amount']);
        }
    }
    echo "Bookings and Payments inserted.<br>";

    // --- 5. Expenses ---
    $expense_stmt = $pdo->prepare("INSERT INTO expenses (project_id, amount, expense_date, description, category) VALUES (?, ?, ?, ?, ?)");
    $categories = ['Construction', 'Marketing', 'Legal', 'Logistics', 'Salaries'];
    
    foreach ($project_ids as $pid) {
        for ($k = 0; $k < 10; $k++) { // More expenses for better charts
            $amount = rand(5000, 200000);
            $date = date('Y-m-d', strtotime("-" . rand(1, 180) . " days"));
            $cat = $categories[array_rand($categories)];
            $desc = "$cat expense for project";
            $expense_stmt->execute([$pid, $amount, $date, $desc, $cat]);
        }
    }
    echo "Expenses inserted.<br>";

    // --- 6. Partners & Ledger ---
    $partners = [
        ['Rajesh Kumar', 40],
        ['Suresh Reddy', 30],
        ['Mahesh Verma', 30]
    ];
    
    $partner_stmt = $pdo->prepare("INSERT INTO partners (name, capital_contribution) VALUES (?, ?)");
    $ledger_stmt = $pdo->prepare("INSERT INTO partner_ledger (partner_id, transaction_date, amount, mode, remarks, type, bank_id, receipt_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($partners as $p) {
        // Initial capital is 0, will be summed from ledger
        $partner_stmt->execute([$p[0], 0]);
        $pid = $pdo->lastInsertId();
        
        // Add some capital transactions
        $capital = rand(50, 200) * 100000;
        $date = date('Y-m-d', strtotime("-6 months"));
        $bank_id = $bank_ids[array_rand($bank_ids)];
        $ledger_stmt->execute([$pid, $date, $capital, 'Bank Transfer', 'Initial Capital', 'Credit', $bank_id, 'RCPT-'.rand(100,999)]);
        
        // Update partner total
        $pdo->exec("UPDATE partners SET capital_contribution = capital_contribution + $capital WHERE id = $pid");
        
        // Another injection
        if (rand(0, 1)) {
            $amt = rand(10, 50) * 100000;
            $date = date('Y-m-d', strtotime("-3 months"));
            $bank_id = $bank_ids[array_rand($bank_ids)];
            $ledger_stmt->execute([$pid, $date, $amt, 'Cheque', 'Additional Capital', 'Credit', $bank_id, 'RCPT-'.rand(100,999)]);
            $pdo->exec("UPDATE partners SET capital_contribution = capital_contribution + $amt WHERE id = $pid");
        }
        
        // Withdrawal
        if (rand(0, 1)) {
            $amt = rand(5, 20) * 100000;
            $date = date('Y-m-d', strtotime("-1 month"));
            $bank_id = $bank_ids[array_rand($bank_ids)];
            $ledger_stmt->execute([$pid, $date, $amt, 'Bank Transfer', 'Personal Withdrawal', 'Debit', $bank_id, 'RCPT-'.rand(100,999)]);
            $pdo->exec("UPDATE partners SET capital_contribution = capital_contribution - $amt WHERE id = $pid");
        }
    }
    echo "Partners and Ledger inserted.<br>";

    // --- 7. Materials & Vendors ---
    $materials = [
        ['Cement', 'Bags', 350],
        ['Steel', 'Kg', 65],
        ['Bricks', 'Nos', 8],
        ['Sand', 'Cubic Ft', 55],
        ['Paint', 'Liters', 400]
    ];
    $mat_stmt = $pdo->prepare("INSERT INTO materials (category, unit_measure, standard_rate, status) VALUES (?, ?, ?, 'Active')");
    foreach ($materials as $m) {
        $mat_stmt->execute($m);
    }

    $vendors = [
        ['BuildMat Suppliers', 'GSTIN12345', 'Contact: 9876543210', 'Bank: HDFC 123456', 'Cement, Steel'],
        ['Color World', 'GSTIN67890', 'Contact: 8765432109', 'Bank: SBI 987654', 'Paint'],
        ['Standard Bricks', 'GSTIN54321', 'Contact: 7654321098', 'Bank: ICICI 456789', 'Bricks, Sand']
    ];
    $vend_stmt = $pdo->prepare("INSERT INTO vendors (name, gst_number, contact_details, bank_details, material_category, status) VALUES (?, ?, ?, ?, ?, 'Active')");
    foreach ($vendors as $v) {
        $vend_stmt->execute($v);
    }
    echo "Materials and Vendors inserted.<br>";

    echo "Database population completed successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
