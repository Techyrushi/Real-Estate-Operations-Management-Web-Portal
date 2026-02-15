<?php
include 'includes/header.php';
include 'includes/sidebar.php';

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_partners')) {
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
            $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
            if ($stmt->execute([$delete_id])) {
                $msg = 'Partner deleted successfully.';
            } else {
                $error = 'Unable to delete partner.';
            }
        } catch (PDOException $e) {
            $error = 'Unable to delete partner.';
        }
    }
}

// Filters
$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

// Fetch partners with filters
$sql = "SELECT * FROM partners WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($status_filter !== '') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";

// Fetch partners
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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

// Build export query string (preserve current filters)
$export_params = $_GET;
unset($export_params['action'], $export_params['id']);
$export_params['export'] = 'excel';
$export_query = http_build_query($export_params);

// Excel export
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    if (!empty($partners)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="partners_master_' . date('Ymd_His') . '.xls"');
        echo "<table border='1'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Share (%)</th>
                <th>Opening Capital</th>
                <th>Current Capital</th>
                <th>Status</th>
                <th>Created At</th>
              </tr>";
        $index = 1;
        foreach ($partners as $row) {
            echo "<tr>";
            echo "<td>" . ($index++) . "</td>";
            echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['email'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['mobile'] ?? '') . "</td>";
            echo "<td>" . number_format($row['percentage_share'] ?? 0, 2) . "</td>";
            echo "<td>" . number_format($row['opening_capital'] ?? 0, 2) . "</td>";
            echo "<td>" . number_format($row['current_capital'] ?? 0, 2) . "</td>";
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
                    <h4 class="page-title">Partner Master</h4>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Partners</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="ms-auto">
                    <a href="admin_partners.php?<?php echo htmlspecialchars($export_query); ?>" class="btn btn-success btn-sm me-2"><i class="ti-download"></i> Export Excel</a>
                    <a href="admin_partner_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New
                        Partner</a>
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
                            <form method="GET" action="admin_partners.php" class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by name, email, mobile"
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo $status_filter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex">
                                    <button type="submit" class="btn btn-primary me-2"><i
                                            class="ti-search"></i></button>
                                    <a href="admin_partners.php" class="btn btn-secondary"><i class="ti-reload"></i></a>
                                </div>
                            </form>
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
                                        <?php
                                        $index = 1;
                                        if (empty($partners)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No partners found.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($partners as $partner): ?>
                                                <tr>
                                                    <td>#<?php echo $index++; ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm bg-primary-light rounded-circle me-2">
                                                                <span
                                                                    class="fs-16"><?php echo strtoupper(substr($partner['name'] ?? '', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0 fw-bold">
                                                                    <?php echo htmlspecialchars($partner['name'] ?? ''); ?></h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <i class="ti-email"></i>
                                                        <?php echo htmlspecialchars($partner['email'] ?? ''); ?><br>
                                                        <i class="ti-mobile"></i>
                                                        <?php echo htmlspecialchars($partner['mobile'] ?? ''); ?>
                                                    </td>
                                                    <td><?php echo number_format($partner['percentage_share'] ?? 0, 2); ?>%</td>
                                                    <td>₹ <?php echo number_format($partner['opening_capital'] ?? 0, 2); ?></td>
                                                    <td><span class="text-success fw-bold">₹
                                                            <?php echo number_format($partner['current_capital'] ?? 0, 2); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = $partner['status'] ?? 'Active';
                                                        $badgeClass = ($status == 'Active') ? 'badge-success' : 'badge-danger';
                                                        ?>
                                                        <span
                                                            class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                                    </td>
                                                    <td>
                                                        <!-- <a href="admin_partner_form.php?id=<?php echo $partner['id'] ?? ''; ?>"
                                                            class="btn btn-sm btn-secondary me-1" data-bs-toggle="tooltip"
                                                            title="View"><i class="ti-eye"></i></a> -->
                                                        <a href="admin_partner_form.php?id=<?php echo $partner['id'] ?? ''; ?>"
                                                            class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip"
                                                            title="Edit"><i class="ti-pencil"></i></a>
                                                        <a href="admin_partner_ledger.php?id=<?php echo $partner['id'] ?? ''; ?>"
                                                            class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip"
                                                            title="View Ledger"><i class="ti-wallet"></i></a>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                            title="Delete"
                                                            onclick="confirmDeletePartner(<?php echo (int) ($partner['id'] ?? 0); ?>)"><i
                                                                class="ti-trash"></i></button>
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
function confirmDeletePartner(id) {
    if (!id) return;
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the partner and related ledger records.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_partners.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
