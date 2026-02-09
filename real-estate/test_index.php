<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting test...\n";

include 'config/db.php';
echo "DB Connected.\n";

try {
    // Total Projects
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
    echo "Total Projects: " . $stmt->fetchColumn() . "\n";

    // Total Units
    $stmt = $pdo->query("SELECT COUNT(*) FROM units");
    echo "Total Units: " . $stmt->fetchColumn() . "\n";

    // Sold Units
    $stmt = $pdo->query("SELECT COUNT(*) FROM units WHERE status = 'Sold'");
    echo "Sold Units: " . $stmt->fetchColumn() . "\n";

    // Available Units
    $stmt = $pdo->query("SELECT COUNT(*) FROM units WHERE status = 'Available'");
    echo "Available Units: " . $stmt->fetchColumn() . "\n";

    // Total Collections (Income)
    $stmt = $pdo->query("SELECT SUM(amount) FROM payments");
    echo "Total Income: " . ($stmt->fetchColumn() ?: 0) . "\n";

    // Total Expenses
    $stmt = $pdo->query("SELECT SUM(amount) FROM expenses");
    echo "Total Expenses: " . ($stmt->fetchColumn() ?: 0) . "\n";

    // Receivables
    $stmt = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status != 'Cancelled'");
    $total_booked_value = $stmt->fetchColumn() ?: 0;
    // $stats['receivables'] = $total_booked_value - $stats['total_income'];
    echo "Total Booked Value: " . $total_booked_value . "\n";

    // Project Types
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM projects GROUP BY type");
    $project_types = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    echo "Project Types: " . json_encode($project_types) . "\n";

    // Partner Capital
    $stmt = $pdo->query("SELECT * FROM partners ORDER BY capital_contribution DESC");
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Partners: " . count($partners) . "\n";

    echo "Test Complete.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
?>
