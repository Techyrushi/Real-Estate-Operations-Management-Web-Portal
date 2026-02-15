<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_banks')) {
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
            $stmt = $pdo->prepare("DELETE FROM banks WHERE id = ?");
            if ($stmt->execute([$delete_id])) {
                $msg = 'Bank deleted successfully.';
            } else {
                $error = 'Unable to delete bank.';
            }
        } catch (PDOException $e) {
            $error = 'Unable to delete bank.';
        }
    }
}

// Filters
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['account_type'] ?? '';
$project_search = trim($_GET['project'] ?? '');

// Build export query string (preserve current filters)
$export_params = $_GET;
unset($export_params['action'], $export_params['id']);
$export_params['export'] = 'excel';
$export_query = http_build_query($export_params);

// Fetch all banks with project name and filters
$sql = "
    SELECT b.*, p.name as project_name 
    FROM banks b 
    LEFT JOIN projects p ON b.project_id = p.id 
    WHERE 1=1
";
$params = [];

if ($project_search !== '') {
    $sql .= " AND (p.name LIKE ? OR b.bank_name LIKE ?)";
    $like = '%' . $project_search . '%';
    $params[] = $like;
    $params[] = $like;
}

if ($status_filter !== '') {
    $sql .= " AND b.status = ?";
    $params[] = $status_filter;
}

if ($type_filter !== '') {
    $sql .= " AND b.account_type = ?";
    $params[] = $type_filter;
}

$sql .= " ORDER BY b.created_at DESC";

// Fetch banks list
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$banks = $stmt->fetchAll();

// Excel export
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    if (!empty($banks)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="banks_master_' . date('Ymd_His') . '.xls"');
        echo "<table border='1'>";
        echo "<tr>
                <th>Bank Name</th>
                <th>Branch</th>
                <th>Account Number</th>
                <th>IFSC</th>
                <th>Project</th>
                <th>Account Type</th>
                <th>Status</th>
                <th>Created At</th>
              </tr>";
        foreach ($banks as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['bank_name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['branch'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['account_number'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['ifsc_code'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['project_name'] ?? 'General') . "</td>";
            echo "<td>" . htmlspecialchars($row['account_type'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['status'] ?? '') . "</td>";
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
                <a href="admin_banks.php?<?php echo htmlspecialchars($export_query); ?>" class="btn btn-success btn-sm me-2"><i class="ti-download"></i> Export Excel</a>
                <a href="admin_bank_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Bank</a>
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
                        <form method="GET" action="admin_banks.php" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" name="project" class="form-control" placeholder="Search by project or bank" value="<?php echo htmlspecialchars($project_search); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="account_type" class="form-select">
                                    <option value="">All Account Types</option>
                                    <option value="RERA Escrow" <?php echo $type_filter === 'RERA Escrow' ? 'selected' : ''; ?>>RERA Escrow</option>
                                    <option value="Construction" <?php echo $type_filter === 'Construction' ? 'selected' : ''; ?>>Construction</option>
                                    <option value="Collection" <?php echo $type_filter === 'Collection' ? 'selected' : ''; ?>>Collection</option>
                                    <option value="Other" <?php echo $type_filter === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $status_filter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-primary me-2"><i class="ti-search"></i></button>
                                <a href="admin_banks.php" class="btn btn-secondary"><i class="ti-reload"></i></a>
                            </div>
                        </form>
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
                                    <?php if (empty($banks)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Data Not Found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($banks as $bank): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($bank['bank_name'] ?? ''); ?></strong></td>
                                            <td><?php echo htmlspecialchars($bank['branch'] ?? ''); ?></td>
                                            <td><?php echo str_repeat('X', max(0, strlen($bank['account_number'] ?? '0000') - 4)) . substr($bank['account_number'] ?? '0000', -4); ?></td>
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
                                                <!-- <a href="admin_bank_form.php?id=<?php echo $bank['id'] ?? ''; ?>" class="btn btn-sm btn-secondary me-1" data-bs-toggle="tooltip" title="View"><i class="ti-eye"></i></a> -->
                                                <a href="admin_bank_form.php?id=<?php echo $bank['id'] ?? ''; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="confirmDeleteBank(<?php echo (int) ($bank['id'] ?? 0); ?>)"><i class="ti-trash"></i></button>
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
function confirmDeleteBank(id) {
    if (!id) return;
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the bank record.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_banks.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
