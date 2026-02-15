<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_expenses')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$msg = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int) $_GET['id'];
    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
            if ($stmt->execute([$delete_id])) {
                $msg = 'Expense deleted successfully.';
            } else {
                $error = 'Unable to delete expense.';
            }
        } catch (PDOException $e) {
            $error = 'Unable to delete expense.';
        }
    }
}

// Fetch Filter Options
$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();
$vendors = $pdo->query("SELECT id, name FROM vendors ORDER BY name ASC")->fetchAll();

$db_error = '';

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

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $expenses = $stmt->fetchAll();
} catch (PDOException $e) {
    $expenses = [];
    $db_error = $e->getMessage();
}
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
                <?php if ($db_error): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($db_error); ?>
                    </div>
                <?php endif; ?>
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
                                    <?php if (empty($expenses)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No expenses found.</td>
                                        </tr>
                                    <?php else: ?>
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
                                                <button type="button"
                                                    class="btn btn-sm btn-secondary me-1"
                                                    data-bs-toggle="tooltip"
                                                    title="View"
                                                    onclick="viewExpense(
                                                        '<?php echo htmlspecialchars(date('d-M-Y', strtotime($expense['expense_date'] ?? 'now')), ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['project_name'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['vendor_name'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['material_name'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars(number_format($expense['amount'] ?? 0, 2), ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars(number_format($expense['gst_amount'] ?? 0, 2), ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['payment_mode'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['bank_name'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['reference_no'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['remarks'] ?? '', ENT_QUOTES); ?>',
                                                        '<?php echo htmlspecialchars($expense['invoice_file'] ?? '', ENT_QUOTES); ?>'
                                                    )">
                                                    <i class="ti-eye"></i>
                                                </button>
                                                <a href="admin_expense_form.php?id=<?php echo $expense['id'] ?? ''; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="confirmDeleteExpense(<?php echo (int) ($expense['id'] ?? 0); ?>)"><i class="ti-trash"></i></button>
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

<div class="modal fade" id="expenseViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Date:</strong> <span id="view_expense_date"></span></p>
                        <p><strong>Project:</strong> <span id="view_expense_project"></span></p>
                        <p><strong>Vendor:</strong> <span id="view_expense_vendor"></span></p>
                        <p><strong>Material/Service:</strong> <span id="view_expense_material"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Amount:</strong> ₹ <span id="view_expense_amount"></span></p>
                        <p><strong>GST Amount:</strong> ₹ <span id="view_expense_gst"></span></p>
                        <p><strong>Mode:</strong> <span id="view_expense_mode"></span></p>
                        <p><strong>Bank:</strong> <span id="view_expense_bank"></span></p>
                    </div>
                </div>
                <p><strong>Reference No:</strong> <span id="view_expense_ref"></span></p>
                <p><strong>Remarks:</strong></p>
                <p id="view_expense_remarks"></p>
                <p><strong>Invoice:</strong> <a href="#" id="view_expense_invoice_link" target="_blank" style="display:none;">View Invoice</a></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteExpense(id) {
    if (!id) return;
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the expense record.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_expenses.php?action=delete&id=' + id;
        }
    });
}

function viewExpense(date, project, vendor, material, amount, gst, mode, bank, refNo, remarks, invoiceUrl) {
    document.getElementById('view_expense_date').innerText = date || '';
    document.getElementById('view_expense_project').innerText = project || '';
    document.getElementById('view_expense_vendor').innerText = vendor || '';
    document.getElementById('view_expense_material').innerText = material || '';
    document.getElementById('view_expense_amount').innerText = amount || '0.00';
    document.getElementById('view_expense_gst').innerText = gst || '0.00';
    document.getElementById('view_expense_mode').innerText = mode || '';
    document.getElementById('view_expense_bank').innerText = bank || '';
    document.getElementById('view_expense_ref').innerText = refNo || '';
    document.getElementById('view_expense_remarks').innerText = remarks || '';

    var link = document.getElementById('view_expense_invoice_link');
    if (invoiceUrl) {
        link.href = invoiceUrl;
        link.style.display = 'inline';
    } else {
        link.href = '#';
        link.style.display = 'none';
    }

    var modal = new bootstrap.Modal(document.getElementById('expenseViewModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>
