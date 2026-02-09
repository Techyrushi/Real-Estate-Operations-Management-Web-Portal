<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_expenses')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch Filter Options
$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();
$vendors = $pdo->query("SELECT id, name FROM vendors ORDER BY name ASC")->fetchAll();

// Build Query
$sql = "SELECT e.*, p.name as project_name, v.name as vendor_name, m.name as material_name, b.bank_name, b.account_number 
        FROM expenses e 
        LEFT JOIN projects p ON e.project_id = p.id 
        LEFT JOIN vendors v ON e.vendor_id = v.id 
        LEFT JOIN materials m ON e.material_id = m.id 
        LEFT JOIN banks b ON e.bank_id = b.id 
        WHERE 1=1";

$params = [];

if (!empty($_GET['project_id'])) {
    $sql .= " AND e.project_id = ?";
    $params[] = $_GET['project_id'];
}

if (!empty($_GET['vendor_id'])) {
    $sql .= " AND e.vendor_id = ?";
    $params[] = $_GET['vendor_id'];
}

if (!empty($_GET['start_date'])) {
    $sql .= " AND e.expense_date >= ?";
    $params[] = $_GET['start_date'];
}

if (!empty($_GET['end_date'])) {
    $sql .= " AND e.expense_date <= ?";
    $params[] = $_GET['end_date'];
}

$sql .= " ORDER BY e.expense_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Expense Payments Ledger</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Expenses</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_expense_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Expense</a>
            </div>
        </div>
    </div>

    <section class="content">
        <!-- Filter Section -->
        <div class="box">
            <div class="box-body">
                <form method="get" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Project</label>
                            <select name="project_id" class="form-select">
                                <option value="">All Projects</option>
                                <?php foreach ($projects as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" <?php echo ($_GET['project_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($p['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Vendor</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">All Vendors</option>
                                <?php foreach ($vendors as $v): ?>
                                    <option value="<?php echo $v['id']; ?>" <?php echo ($_GET['vendor_id'] ?? '') == $v['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-info w-100 me-2">Filter</button>
                        <a href="admin_expenses.php" class="btn btn-secondary w-100">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="expenses_table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Project</th>
                                        <th>Vendor</th>
                                        <th>Material/Service</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Bank</th>
                                        <th>Ref No</th>
                                        <th>Invoice</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td><?php echo date('d-M-Y', strtotime($expense['expense_date'] ?? 'now')); ?></td>
                                        <td><?php echo htmlspecialchars($expense['project_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($expense['vendor_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($expense['material_name'] ?? ''); ?></td>
                                        <td>
                                            <span class="fw-bold">₹ <?php echo number_format($expense['amount'] ?? 0, 2); ?></span>
                                            <?php if(($expense['gst_amount'] ?? 0) > 0): ?>
                                                <br><small class="text-muted">+GST: <?php echo number_format($expense['gst_amount'], 2); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($expense['payment_mode'] ?? ''); ?></td>
                                        <td>
                                            <?php if($expense['bank_name'] ?? false): ?>
                                                <?php echo htmlspecialchars($expense['bank_name']); ?>
                                                <br><small class="text-muted"><?php echo str_repeat('X', strlen($expense['account_number'] ?? '0000') - 4) . substr($expense['account_number'] ?? '0000', -4); ?></small>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($expense['reference_no'] ?? ''); ?></td>
                                        <td>
                                            <?php if(!empty($expense['invoice_file'])): ?>
                                                <a href="<?php echo htmlspecialchars($expense['invoice_file']); ?>" target="_blank" class="btn btn-xs btn-primary"><i class="ti-download"></i> View</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="admin_expense_form.php?id=<?php echo $expense['id'] ?? ''; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
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
