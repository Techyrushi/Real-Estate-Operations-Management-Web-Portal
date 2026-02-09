<?php
include 'config/db.php';

echo "Starting Database Population...\n";

try {
    // 1. Projects
    $projects = [
        ['name' => 'Sunset Villa', 'type' => 'Residential', 'description' => 'Luxury villas with sea view.'],
        ['name' => 'Downtown Office Tower', 'type' => 'Commercial', 'description' => 'Modern office spaces in the city center.'],
        ['name' => 'Green Valley Apartments', 'type' => 'Residential', 'description' => 'Eco-friendly apartments.'],
        ['name' => 'City Mall Complex', 'type' => 'Commercial', 'description' => 'Shopping mall and entertainment center.'],
        ['name' => 'Mixed Use Plaza', 'type' => 'Mixed', 'description' => 'Retail and residential units.']
    ];

    $pdo->exec("DELETE FROM projects"); // Clear existing
    $pdo->exec("ALTER TABLE projects AUTO_INCREMENT = 1");

    $stmt = $pdo->prepare("INSERT INTO projects (name, type, description) VALUES (:name, :type, :description)");
    foreach ($projects as $project) {
        $stmt->execute($project);
    }
    echo "Inserted " . count($projects) . " projects.\n";

    // 2. Units
    $pdo->exec("DELETE FROM units");
    $pdo->exec("ALTER TABLE units AUTO_INCREMENT = 1");
    
    $units = [];
    $project_ids = $pdo->query("SELECT id FROM projects")->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $pdo->prepare("INSERT INTO units (project_id, unit_number, status, price, type) VALUES (:project_id, :unit_number, :status, :price, :type)");
    
    $unit_types = ['1BHK', '2BHK', '3BHK', 'Studio', 'Office', 'Shop'];
    $statuses = ['Available', 'Sold', 'Reserved'];

    for ($i = 1; $i <= 50; $i++) {
        $proj_id = $project_ids[array_rand($project_ids)];
        $type = $unit_types[array_rand($unit_types)];
        $status = $statuses[array_rand($statuses)];
        $price = rand(100000, 5000000);
        
        // Note: schema.sql didn't have 'type' column in 'units' table in my previous read, but index.php queries u.type.
        // Let's check schema.sql again. If it's missing, I'll add it.
        // Assuming I might need to add it. For now, I'll try to insert. 
        // Wait, index.php line 76: u.type as unit_type.
        // Let's check schema.sql content I read earlier.
        // schema.sql:
        // CREATE TABLE IF NOT EXISTS units (
        //     id INT AUTO_INCREMENT PRIMARY KEY,
        //     project_id INT NOT NULL,
        //     unit_number VARCHAR(50) NOT NULL,
        //     status ENUM('Available', 'Sold', 'Reserved') DEFAULT 'Available',
        //     price DECIMAL(15, 2) NOT NULL,
        //     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        //     FOREIGN KEY (project_id) REFERENCES projects(id)
        // );
        // It does NOT have 'type'. I must add it.
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
