<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_customers')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch Customers with Booking Details
$sql = "SELECT c.*, b.booking_date, b.total_price as total_deal_amount, b.status as booking_status, 
               p.name as project_name, u.unit_number
        FROM customers c 
        LEFT JOIN bookings b ON c.id = b.customer_id
        LEFT JOIN units u ON b.unit_id = u.id
        LEFT JOIN projects p ON u.project_id = p.id
        ORDER BY c.created_at DESC";
$customers = $pdo->query($sql)->fetchAll();
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
                <a href="admin_customer_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Customer</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
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
                                    <?php foreach ($customers as $c): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></span>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($c['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($c['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($c['project_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($c['unit_number'] ?? '-'); ?></td>
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
                                            <a href="admin_customer_form.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                            <a href="admin_customer_ledger.php?customer_id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Ledger"><i class="ti-wallet"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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

<?php include 'includes/footer.php'; ?>
