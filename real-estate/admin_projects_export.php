<?php
include_once 'config/db.php';
include_once 'includes/auth_session.php';

if (!hasRole('Admin') && !hasPermission('manage_projects')) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access Denied';
    exit();
}

$filter_type = $_GET['type'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_city = $_GET['city'] ?? '';
$filter_search = trim($_GET['search'] ?? '');
$filter_followed = $_GET['followed'] ?? '';

$sql = "SELECT * FROM projects WHERE 1=1";
$params = [];

if ($filter_type !== '') {
    $sql .= " AND type = ?";
    $params[] = $filter_type;
}

if ($filter_status !== '') {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}

if ($filter_city !== '') {
    $sql .= " AND city = ?";
    $params[] = $filter_city;
}

if ($filter_search !== '') {
    $sql .= " AND (name LIKE ? OR rera_reg LIKE ? OR address LIKE ?)";
    $like = '%' . $filter_search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($filter_followed === '1' && isset($_SESSION['user_id'])) {
    $sql .= " AND id IN (SELECT entity_id FROM user_follows WHERE user_id = ? AND entity_type = 'project')";
    $params[] = (int) $_SESSION['user_id'];
}

$sql .= " ORDER BY created_at DESC";
$index = 1;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=projects_master_export_' . date('Ymd_His') . '.xls');

echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Address</th>
        <th>City</th>
        <th>State</th>
        <th>Location Details</th>
        <th>RERA Reg</th>
        <th>Ready Reckoner Rate (Res)</th>
        <th>Ready Reckoner Rate (Com)</th>
        <th>Carpet Area</th>
        <th>Sellable Area</th>
        <th>Num Units</th>
        <th>Status</th>
      </tr>";

foreach ($projects as $row) {
    echo "<tr>";
    echo "<td>" . ($index++) . "</td>"; 
    echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['type'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['description'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['address'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['city'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['state'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['location_details'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['rera_reg'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['ready_reckoner_rate_res'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['ready_reckoner_rate_com'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['carpet_area'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['sellable_area'] ?? '') . "</td>";
    echo "<td>" . (int) ($row['num_units'] ?? 0) . "</td>";
    echo "<td>" . htmlspecialchars($row['status'] ?? '') . "</td>";
    echo "</tr>";
}

echo "</table>";
