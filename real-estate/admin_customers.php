<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_customers')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$msg = '';
$error = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int) $_GET['id'];
    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = ?");
            $stmt->execute([$delete_id]);
            $bookingCount = (int) $stmt->fetchColumn();
            if ($bookingCount > 0) {
                $error = "Cannot delete customer because bookings exist. Cancel or delete bookings first.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
                if ($stmt->execute([$delete_id])) {
                    $msg = "Customer deleted successfully.";
                } else {
                    $error = "Failed to delete customer.";
                }
            }
        } catch (PDOException $e) {
            $error = "Unable to delete customer.";
        }
    }
}

// Fetch Customers with Booking Details
$sql = "SELECT c.*, b.booking_date, b.total_price as total_deal_amount, b.status as booking_status, 
               p.name as project_name, u.flat_no    
        FROM customers c 
        LEFT JOIN bookings b ON c.id = b.customer_id
        LEFT JOIN units u ON b.unit_id = u.id
        LEFT JOIN projects p ON u.project_id = p.id
        ORDER BY c.created_at DESC";
$customers = $pdo->query($sql)->fetchAll();

// Build export query string
$export_params = $_GET;
unset($export_params['action'], $export_params['id']);
$export_params['export'] = 'excel';
$export_query = http_build_query($export_params);

// Excel export
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    if (!empty($customers)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="customers_master_' . date('Ymd_His') . '.xls"');
        echo "<table border='1'>";
        echo "<tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Project</th>
                <th>Unit</th>
                <th>Booking Date</th>
                <th>Total Deal</th>
                <th>Status</th>
                <th>Created At</th>
              </tr>";
        foreach ($customers as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['mobile'] ?? $row['phone'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['email'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['project_name'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['flat_no'] ?? '-') . "</td>";
            echo "<td>" . (!empty($row['booking_date']) ? htmlspecialchars($row['booking_date']) : '-') . "</td>";
            echo "<td>" . (!empty($row['total_deal_amount']) ? number_format($row['total_deal_amount'], 2) : '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['booking_status'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Data Not Found";
    }
    exit;
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Customer Master</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customers</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_customers.php?<?php echo htmlspecialchars($export_query); ?>" class="btn btn-success btn-sm me-2"><i class="ti-download"></i> Export Excel</a>
                <a href="admin_customer_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Customer</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="customers_table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Project</th>
                                        <th>Unit</th>
                                        <th>Booking Date</th>
                                        <th>Total Deal</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($customers)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No customers found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($customers as $c): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></span>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($c['email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($c['phone'] ?? $c['mobile'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($c['project_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($c['flat_no'] ?? '-'); ?></td>  
                                            <td><?php echo $c['booking_date'] ? date('d-M-Y', strtotime($c['booking_date'])) : '-'; ?></td>
                                            <td><?php echo $c['total_deal_amount'] ? '₹ ' . number_format($c['total_deal_amount'], 2) : '-'; ?></td>
                                            <td>
                                                <?php if ($c['booking_status']): ?>
                                                <span class="badge badge-<?php 
                                                    echo match($c['booking_status']) {
                                                        'Confirmed' => 'success',
                                                        'Pending' => 'warning',
                                                        'Cancelled' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo $c['booking_status']; ?>
                                                </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">No Booking</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="admin_customer_form.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-secondary me-1" data-bs-toggle="tooltip" title="View"><i class="ti-eye"></i></a>
                                                <a href="admin_customer_form.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                                <a href="admin_customer_ledger.php?customer_id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Ledger"><i class="ti-wallet"></i></a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="confirmDeleteCustomer(<?php echo (int) ($c['id'] ?? 0); ?>)"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<script>
function confirmDeleteCustomer(id) {
    if (!id) return;
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the customer record.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_customers.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
