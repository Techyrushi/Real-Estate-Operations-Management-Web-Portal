<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_banks')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch all banks with project name
$stmt = $pdo->query("
    SELECT b.*, p.name as project_name 
    FROM banks b 
    LEFT JOIN projects p ON b.project_id = p.id 
    ORDER BY b.created_at DESC
");
$banks = $stmt->fetchAll();
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Bank Master</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Banks</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_bank_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Bank</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="banks_table">
                                <thead>
                                    <tr>
                                        <th>Bank Name</th>
                                        <th>Branch</th>
                                        <th>Account No</th>
                                        <th>IFSC</th>
                                        <th>Project</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($banks as $bank): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($bank['bank_name'] ?? ''); ?></strong></td>
                                        <td><?php echo htmlspecialchars($bank['branch'] ?? ''); ?></td>
                                        <td><?php echo str_repeat('X', strlen($bank['account_number'] ?? '0000') - 4) . substr($bank['account_number'] ?? '0000', -4); ?></td>
                                        <td><?php echo htmlspecialchars($bank['ifsc_code'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($bank['project_name'] ?? 'General'); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($bank['account_type'] ?? ''); ?></span></td>
                                        <td>
                                            <?php 
                                            $status = $bank['status'] ?? 'Active';
                                            $badgeClass = ($status == 'Active') ? 'badge-success' : 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                        </td>
                                        <td>
                                            <a href="admin_bank_form.php?id=<?php echo $bank['id'] ?? ''; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
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