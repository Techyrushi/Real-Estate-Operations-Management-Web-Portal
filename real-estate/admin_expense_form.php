<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_expenses')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$expense = null;
$msg = "";
$error = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
    $stmt->execute([$id]);
    $expense = $stmt->fetch();
    if (!$expense) {
        $error = "Expense not found.";
    }
}

// Fetch Dropdowns
$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();
$vendors = $pdo->query("SELECT id, name FROM vendors ORDER BY name ASC")->fetchAll();
$materials = $pdo->query("SELECT id, name FROM materials ORDER BY name ASC")->fetchAll();
$banks = $pdo->query("SELECT id, bank_name, account_number, project_id FROM banks ORDER BY bank_name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = $_POST['project_id'] ?? '';
    $vendor_id = !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : null;
    $material_id = !empty($_POST['material_id']) ? $_POST['material_id'] : null;
    $expense_date = $_POST['expense_date'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $gst_amount = !empty($_POST['gst_amount']) ? $_POST['gst_amount'] : 0;
    $payment_mode = $_POST['payment_mode'] ?? '';
    $bank_id = !empty($_POST['bank_id']) ? $_POST['bank_id'] : null;
    $reference_no = $_POST['reference_no'] ?? '';
    $remarks = $_POST['remarks'] ?? '';

    // Handle File Upload
    $invoice_path = $expense['invoice_file'] ?? '';
    if (!empty($_FILES['invoice_file']['name'])) {
        $target_dir = "../uploads/invoices/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES["invoice_file"]["name"], PATHINFO_EXTENSION));
        $new_filename = "inv_" . time() . "_" . rand(1000,9999) . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["invoice_file"]["tmp_name"], $target_file)) {
            $invoice_path = $target_file;
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    if (!$error) {
        try {
            if ($id) {
                $sql = "UPDATE expenses SET project_id=?, vendor_id=?, material_id=?, expense_date=?, amount=?, gst_amount=?, payment_mode=?, bank_id=?, reference_no=?, remarks=?, invoice_file=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$project_id, $vendor_id, $material_id, $expense_date, $amount, $gst_amount, $payment_mode, $bank_id, $reference_no, $remarks, $invoice_path, $id])) {
                    $msg = "Expense updated successfully.";
                    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
                    $stmt->execute([$id]);
                    $expense = $stmt->fetch();
                } else {
                    $error = "Failed to update expense.";
                }
            } else {
                $sql = "INSERT INTO expenses (project_id, vendor_id, material_id, expense_date, amount, gst_amount, payment_mode, bank_id, reference_no, remarks, invoice_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$project_id, $vendor_id, $material_id, $expense_date, $amount, $gst_amount, $payment_mode, $bank_id, $reference_no, $remarks, $invoice_path])) {
                    $msg = "Expense added successfully.";
                    $id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
                    $stmt->execute([$id]);
                    $expense = $stmt->fetch();
                } else {
                    $error = "Failed to add expense.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title"><?php echo $id ? 'Edit Expense' : 'Add New Expense'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_expenses.php">Expenses</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $id ? 'Edit' : 'Add'; ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: '<?php echo $msg; ?>',
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'admin_expenses.php';
                                        }
                                    });
                                });
                            </script>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: '<?php echo $error; ?>'
                                    });
                                });
                            </script>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="expense_date" value="<?php echo $expense['expense_date'] ?? date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Project <span class="text-danger">*</span></label>
                                        <select class="form-select" name="project_id" required>
                                            <option value="">Select Project</option>
                                            <?php foreach ($projects as $p): ?>
                                                <option value="<?php echo $p['id']; ?>" <?php echo ($expense['project_id'] ?? '') == $p['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Vendor</label>
                                        <select class="form-select" name="vendor_id">
                                            <option value="">Select Vendor</option>
                                            <?php foreach ($vendors as $v): ?>
                                                <option value="<?php echo $v['id']; ?>" <?php echo ($expense['vendor_id'] ?? '') == $v['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($v['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Material / Service</label>
                                        <select class="form-select" name="material_id">
                                            <option value="">Select Material</option>
                                            <?php foreach ($materials as $m): ?>
                                                <option value="<?php echo $m['id']; ?>" <?php echo ($expense['material_id'] ?? '') == $m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $expense['amount'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">GST Amount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="gst_amount" value="<?php echo $expense['gst_amount'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_mode" required>
                                            <option value="Bank Transfer" <?php echo ($expense['payment_mode'] ?? '') == 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="Cheque" <?php echo ($expense['payment_mode'] ?? '') == 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                                            <option value="Cash" <?php echo ($expense['payment_mode'] ?? '') == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                            <option value="UPI" <?php echo ($expense['payment_mode'] ?? '') == 'UPI' ? 'selected' : ''; ?>>UPI</option>
                                            <option value="Other" <?php echo ($expense['payment_mode'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Bank Account Used</label>
                                        <select class="form-select" name="bank_id">
                                            <option value="">Select Bank Account</option>
                                            <?php foreach ($banks as $b): ?>
                                                <option value="<?php echo $b['id']; ?>" <?php echo ($expense['bank_id'] ?? '') == $b['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($b['bank_name'] . ' - ' . substr($b['account_number'], -4)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Reference / Cheque No.</label>
                                        <input type="text" class="form-control" name="reference_no" value="<?php echo htmlspecialchars($expense['reference_no'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Upload Invoice (Image/PDF)</label>
                                        <input type="file" class="form-control" name="invoice_file" accept="image/*,.pdf">
                                        <?php if(!empty($expense['invoice_file'])): ?>
                                            <p class="mt-2"><a href="<?php echo htmlspecialchars($expense['invoice_file']); ?>" target="_blank">View Current Invoice</a></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control" name="remarks" rows="2"><?php echo htmlspecialchars($expense['remarks'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Expense</button>
                                <a href="admin_expenses.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
