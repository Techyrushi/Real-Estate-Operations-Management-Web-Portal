<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_partners')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$partner_id = $_GET['id'] ?? null;
if (!$partner_id) {
    echo "<script>window.location.href='admin_partners.php';</script>";
    exit();
}

// Fetch Partner Info
$stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
$stmt->execute([$partner_id]);
$partner = $stmt->fetch();

// Fetch Banks
$banks = $pdo->query("SELECT id, bank_name, account_number FROM banks WHERE status = 'Active'")->fetchAll();

if (!$partner) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Partner Not Found</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Handle Transaction
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['transaction_date'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $mode = $_POST['mode'] ?? '';
    $bank_id = $_POST['bank_id'] ?? null;
    if (empty($bank_id)) $bank_id = null;
    $receipt_no = $_POST['receipt_no'] ?? '';
    $type = $_POST['type'] ?? ''; // Credit / Debit
    $remarks = $_POST['remarks'] ?? '';

    $sql = "INSERT INTO partner_ledger (partner_id, transaction_date, amount, mode, remarks, type, bank_id, receipt_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    try {
        if ($stmt->execute([$partner_id, $date, $amount, $mode, $remarks, $type, $bank_id, $receipt_no])) {
            // Update partner's total capital contribution
            if ($type == 'Credit') {
                $pdo->exec("UPDATE partners SET capital_contribution = capital_contribution + $amount WHERE id = $partner_id");
            } else {
                $pdo->exec("UPDATE partners SET capital_contribution = capital_contribution - $amount WHERE id = $partner_id");
            }
            $msg = "Transaction added successfully.";
        } else {
            $error = "Failed to add transaction.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Fetch Ledger
$stmt = $pdo->prepare("SELECT pl.*, b.bank_name, b.account_number FROM partner_ledger pl LEFT JOIN banks b ON pl.bank_id = b.id WHERE pl.partner_id = ? ORDER BY pl.transaction_date DESC, pl.id DESC");
$stmt->execute([$partner_id]);
$transactions = $stmt->fetchAll();

// Calculate Current Balance
$total_credit = 0;
$total_debit = 0;
foreach ($transactions as $t) {
    if ($t['type'] == 'Credit') $total_credit += $t['amount'];
    if ($t['type'] == 'Debit') $total_debit += $t['amount'];
}
$current_balance = $partner['opening_capital'] + $total_credit - $total_debit;
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Partner Ledger: <?php echo htmlspecialchars($partner['name']); ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_partners.php">Partners</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ledger</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transactionModal"><i class="ti-plus"></i> Add Transaction</button>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="bg-primary-light p-3 rounded">
                                    <h5>Opening Capital</h5>
                                    <h3>₹ <?php echo number_format($partner['opening_capital'], 2); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-success-light p-3 rounded">
                                    <h5>Total Invested (Credit)</h5>
                                    <h3>₹ <?php echo number_format($total_credit, 2); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-danger-light p-3 rounded">
                                    <h5>Total Withdrawn (Debit)</h5>
                                    <h3>₹ <?php echo number_format($total_debit, 2); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-info-light p-3 rounded">
                                    <h5>Current Balance</h5>
                                    <h3>₹ <?php echo number_format($current_balance, 2); ?></h3>
                                </div>
                            </div>
                        </div>

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
                                            window.location.href = 'admin_partner_ledger.php?id=<?php echo $partner_id; ?>';
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

                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="ledger_table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt/Ref No</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Bank</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($t['transaction_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($t['receipt_no'] ?? '-'); ?></td>
                                        <td>
                                            <?php if($t['type'] == 'Credit'): ?>
                                                <span class="badge badge-success">Credit (Invest)</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Debit (Withdraw)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong>₹ <?php echo number_format($t['amount'], 2); ?></strong></td>
                                        <td><?php echo htmlspecialchars($t['mode']); ?></td>
                                        <td><?php echo $t['bank_name'] ? htmlspecialchars($t['bank_name'] . ' (' . $t['account_number'] . ')') : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($t['remarks']); ?></td>
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

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="transaction_date" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-select" name="type" required>
                <option value="Credit">Credit (Investment)</option>
                <option value="Debit">Debit (Withdrawal)</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount (₹)</label>
            <input type="number" step="0.01" class="form-control" name="amount" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Payment Mode</label>
            <select class="form-select" name="mode">
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Bank (Optional)</label>
            <select class="form-select" name="bank_id">
                <option value="">-- Select Bank --</option>
                <?php foreach ($banks as $b): ?>
                    <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['bank_name'] . ' - ' . $b['account_number']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Receipt/Reference No</label>
            <input type="text" class="form-control" name="receipt_no" placeholder="Enter Reference Number">
        </div>
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" name="remarks" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Transaction</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>