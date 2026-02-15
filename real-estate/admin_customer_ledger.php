<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_customers')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$customer_id = $_GET['customer_id'] ?? null;
if (!$customer_id) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>No Customer ID Provided</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch Customer Details with Booking Info
$stmt = $pdo->prepare("SELECT c.*, b.id as booking_id, b.total_price as total_deal_amount, p.name as project_name, u.flat_no 
                       FROM customers c 
                       LEFT JOIN bookings b ON c.id = b.customer_id
                       LEFT JOIN units u ON b.unit_id = u.id
                       LEFT JOIN projects p ON u.project_id = p.id
                       WHERE c.id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Customer Not Found</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Handle New Payment
$msg = "";
$error = "";

// Handle Delete Payment
if (isset($_GET['delete_payment_id'])) {
    $delete_id = $_GET['delete_payment_id'];
    try {
        // Verify it belongs to this booking
        $stmt = $pdo->prepare("SELECT booking_id FROM payments WHERE id = ?");
        $stmt->execute([$delete_id]);
        $p = $stmt->fetch();
        
        if ($p && $p['booking_id'] == $customer['booking_id']) {
            $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
            if ($stmt->execute([$delete_id])) {
                $msg = "Payment deleted successfully.";
            } else {
                $error = "Failed to delete payment.";
            }
        } else {
            $error = "Invalid payment deletion request.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Handle Edit Fetch
$edit_payment = null;
if (isset($_GET['edit_payment_id'])) {
    $edit_id = $_GET['edit_payment_id'];
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND booking_id = ?");
    $stmt->execute([$edit_id, $customer['booking_id']]);
    $edit_payment = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_payment'])) {
    if (!$customer['booking_id']) {
        $error = "No active booking found for this customer. Cannot record payment.";
    } else {
        $payment_date = $_POST['payment_date'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $payment_mode = $_POST['payment_mode'] ?? '';
        $bank_id = !empty($_POST['bank_id']) ? $_POST['bank_id'] : null;
        $receipt_no = $_POST['receipt_no'] ?? '';
        $remarks = $_POST['remarks'] ?? '';
        $update_id = $_POST['update_payment_id'] ?? null;

        if ($amount > 0) {
            try {
                if ($update_id) {
                    // Update
                    $sql = "UPDATE payments SET payment_date=?, amount=?, payment_method=?, bank_id=?, receipt_no=?, remarks=? WHERE id=? AND booking_id=?";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$payment_date, $amount, $payment_mode, $bank_id, $receipt_no, $remarks, $update_id, $customer['booking_id']])) {
                        $msg = "Payment updated successfully.";
                        $edit_payment = null; // Clear edit mode
                    } else {
                        $error = "Failed to update payment.";
                    }
                } else {
                    // Insert
                    $sql = "INSERT INTO payments (booking_id, payment_date, amount, payment_method, bank_id, receipt_no, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$customer['booking_id'], $payment_date, $amount, $payment_mode, $bank_id, $receipt_no, $remarks])) {
                        $msg = "Payment added successfully.";
                    } else {
                        $error = "Failed to add payment.";
                    }
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        } else {
            $error = "Amount must be greater than 0.";
        }
    }
}

// Fetch Payments
$payment_list = [];
$total_paid = 0;
if ($customer['booking_id']) {
    $payments = $pdo->prepare("SELECT p.*, b.bank_name FROM payments p LEFT JOIN banks b ON p.bank_id = b.id WHERE p.booking_id = ? ORDER BY p.payment_date DESC");
    $payments->execute([$customer['booking_id']]);
    $payment_list = $payments->fetchAll();

    // Calculate Totals
    foreach ($payment_list as $p) {
        $total_paid += $p['amount'];
    }
}

$balance_amount = ($customer['total_deal_amount'] ?? 0) - $total_paid;

// Fetch Banks for Dropdown (Collection Accounts)
$banks = $pdo->query("SELECT id, bank_name, account_number FROM banks WHERE account_type IN ('Collection', 'RERA Escrow') ORDER BY bank_name ASC")->fetchAll();
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Customer Payment Ledger</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_customers.php">Customers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ledger</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="print_customer_statement.php?customer_id=<?php echo $customer_id; ?>" class="btn btn-secondary btn-sm"><i class="ti-printer"></i> Print Statement</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <!-- Customer Details Card -->
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h5>Customer: <strong><?php echo htmlspecialchars($customer['name']); ?></strong></h5>
                                <p class="text-muted mb-0"><i class="ti-mobile"></i> <?php echo htmlspecialchars($customer['phone'] ?? $customer['mobile'] ?? ''); ?></p>
                            </div>
                            <div class="col-md-3">
                                <h5>Project: <strong><?php echo htmlspecialchars($customer['project_name'] ?? 'N/A'); ?></strong></h5>
                                <p class="text-muted mb-0">Unit: <strong><?php echo htmlspecialchars($customer['flat_no'] ?? 'N/A'); ?></strong></p>
                            </div>
                            <div class="col-md-2">
                                <h5>Total Deal</h5>
                                <h4 class="text-primary">₹ <?php echo number_format($customer['total_deal_amount'] ?? 0, 2); ?></h4>
                            </div>
                            <div class="col-md-2">
                                <h5>Received</h5>
                                <h4 class="text-success">₹ <?php echo number_format($total_paid, 2); ?></h4>
                            </div>
                            <div class="col-md-2">
                                <h5>Balance</h5>
                                <h4 class="text-danger">₹ <?php echo number_format($balance_amount, 2); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Payment Form -->
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title"><?php echo $edit_payment ? 'Edit Payment' : 'Record New Payment'; ?></h4>
                        <?php if ($edit_payment): ?>
                            <a href="admin_customer_ledger.php?customer_id=<?php echo $customer_id; ?>" class="btn btn-sm btn-outline-secondary float-end">Cancel</a>
                        <?php endif; ?>
                    </div>
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
                                            // Remove query params to prevent re-submission or re-deletion
                                            window.location.href = 'admin_customer_ledger.php?customer_id=<?php echo $customer_id; ?>';
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

                        <script>
                            function confirmDelete(url) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "You won't be able to revert this!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, delete it!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        Swal.fire({
                                            title: 'Processing...',
                                            text: 'Deleting payment...',
                                            allowOutsideClick: false,
                                            didOpen: () => { Swal.showLoading() }
                                        });
                                        window.location.href = url;
                                    }
                                });
                                return false;
                            }
                        </script>
                        
                        <?php if ($customer['booking_id']): ?>
                        <form method="post" action="admin_customer_ledger.php?customer_id=<?php echo $customer_id; ?>" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <input type="hidden" name="add_payment" value="1">
                            <?php if ($edit_payment): ?>
                                <input type="hidden" name="update_payment_id" value="<?php echo $edit_payment['id']; ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payment_date" value="<?php echo $edit_payment['payment_date'] ?? date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo $edit_payment['amount'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select class="form-select" name="payment_mode" required>
                                    <option value="Bank Transfer" <?php echo ($edit_payment['payment_method'] ?? '') == 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                    <option value="Cheque" <?php echo ($edit_payment['payment_method'] ?? '') == 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                                    <option value="Cash" <?php echo ($edit_payment['payment_method'] ?? '') == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                    <option value="UPI" <?php echo ($edit_payment['payment_method'] ?? '') == 'UPI' ? 'selected' : ''; ?>>UPI</option>
                                    <option value="Other" <?php echo ($edit_payment['payment_method'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deposit To Bank</label>
                                <select class="form-select" name="bank_id">
                                    <option value="">Select Bank</option>
                                    <?php foreach ($banks as $b): ?>
                                        <option value="<?php echo $b['id']; ?>" <?php echo ($edit_payment['bank_id'] ?? '') == $b['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['bank_name'] . ' - ' . substr($b['account_number'], -4)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Receipt / Ref No.</label>
                                <input type="text" class="form-control" name="receipt_no" value="<?php echo htmlspecialchars($edit_payment['receipt_no'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="2"><?php echo htmlspecialchars($edit_payment['remarks'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="ti-save-alt"></i> <?php echo $edit_payment ? 'Update Payment' : 'Save Payment'; ?></button>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-warning">No booking associated with this customer. Cannot record payments.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Transaction History</h4>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Particulars</th>
                                        <th>Mode</th>
                                        <th class="text-end">Amount</th>
                                        <th>Receipt No</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payment_list)): ?>
                                        <tr><td colspan="6" class="text-center">No payments recorded yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($payment_list as $p): ?>
                                        <tr>
                                            <td><?php echo date('d-M-Y', strtotime($p['payment_date'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($p['remarks']); ?>
                                                <?php if($p['bank_name']): ?>
                                                    <br><small class="text-muted">Bank: <?php echo htmlspecialchars($p['bank_name']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($p['payment_method']); ?></td>
                                            <td class="text-end fw-bold">₹ <?php echo number_format($p['amount'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($p['receipt_no']); ?></td>
                                            <td>
                                                <a href="admin_customer_ledger.php?customer_id=<?php echo $customer_id; ?>&edit_payment_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-info me-1"><i class="ti-pencil"></i></a>
                                                <a href="#" onclick="return confirmDelete('admin_customer_ledger.php?customer_id=<?php echo $customer_id; ?>&delete_payment_id=<?php echo $p['id']; ?>');" class="btn btn-sm btn-danger"><i class="ti-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="3" class="text-end">Total Paid</th>
                                        <th class="text-end fw-bold text-success">₹ <?php echo number_format($total_paid, 2); ?></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
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
