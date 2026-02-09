<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_partners')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch all partners
$stmt = $pdo->query("SELECT * FROM partners ORDER BY created_at DESC");
$partners = $stmt->fetchAll();

// Calculate Totals for each partner
foreach ($partners as &$partner) {
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN type = 'Credit' THEN amount ELSE 0 END) as total_credit,
        SUM(CASE WHEN type = 'Debit' THEN amount ELSE 0 END) as total_debit
        FROM partner_ledger WHERE partner_id = ?");
    $stmt->execute([$partner['id']]);
    $ledger = $stmt->fetch();
    
    $partner['current_capital'] = $partner['opening_capital'] + ($ledger['total_credit'] ?? 0) - ($ledger['total_debit'] ?? 0);
}
unset($partner); // break reference
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Partner Master</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Partners</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_partner_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Partner</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="partners_table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Partner Name</th>
                                        <th>Contact</th>
                                        <th>Share (%)</th>
                                        <th>Opening Capital</th>
                                        <th>Current Capital</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($partners as $partner): ?>
                                    <tr>
                                        <td>#<?php echo $partner['id'] ?? ''; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary-light rounded-circle me-2"><span class="fs-16"><?php echo strtoupper(substr($partner['name'] ?? '', 0, 1)); ?></span></div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($partner['name'] ?? ''); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="ti-email"></i> <?php echo htmlspecialchars($partner['email'] ?? ''); ?><br>
                                            <i class="ti-mobile"></i> <?php echo htmlspecialchars($partner['mobile'] ?? ''); ?>
                                        </td>
                                        <td><?php echo number_format($partner['percentage_share'] ?? 0, 2); ?>%</td>
                                        <td>₹ <?php echo number_format($partner['opening_capital'] ?? 0, 2); ?></td>
                                        <td><span class="text-success fw-bold">₹ <?php echo number_format($partner['current_capital'] ?? 0, 2); ?></span></td>
                                        <td>
                                            <?php 
                                            $status = $partner['status'] ?? 'Active';
                                            $badgeClass = ($status == 'Active') ? 'badge-success' : 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                        </td>
                                        <td>
                                            <a href="admin_partner_form.php?id=<?php echo $partner['id'] ?? ''; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                            <a href="admin_partner_ledger.php?id=<?php echo $partner['id'] ?? ''; ?>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="View Ledger"><i class="ti-wallet"></i></a>
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