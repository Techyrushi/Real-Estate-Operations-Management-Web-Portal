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

$stmt = $pdo->prepare("SELECT c.*, b.id as booking_id, b.total_price as total_deal_amount, b.booking_date as booking_date,
                              p.name as project_name, u.flat_no as unit_number , p.carpet_area as carpet_area, p.city as city, p.state as state
                       FROM customers c 
                       LEFT JOIN bookings b ON c.id = b.customer_id
                       LEFT JOIN units u ON b.unit_id = u.id
                       LEFT JOIN projects p ON u.project_id = p.id
                       WHERE c.id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Customer Not Found</div></section></div></div>";
    include 'includes.footer.php';
    exit();
}

$payments = $pdo->prepare("SELECT p.*, b.bank_name 
                           FROM payments p 
                           LEFT JOIN banks b ON p.bank_id = b.id 
                           WHERE p.booking_id = ? 
                           ORDER BY p.payment_date ASC");
$payments->execute([$customer['booking_id']]);
$payment_list = $payments->fetchAll();

$total_paid = 0;
foreach ($payment_list as $p) {
    $total_paid += $p['amount'];
}
$balance_amount = $customer['total_deal_amount'] - $total_paid;
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Customer Payment Statement</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_customers.php">Customers</a></li>
                            <li class="breadcrumb-item"><a href="admin_customer_ledger.php?customer_id=<?php echo (int) $customer_id; ?>">Ledger</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Print Statement</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto no-print">
                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="ti-printer"></i> Print / Save PDF
                </button>
            </div>
        </div>
    </div>

    <section class="content">
        <style>
            .statement-wrapper { max-width: 900px; margin: 0 auto; }
            .statement-card { background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08); padding: 24px; }
            .statement-header { border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px; text-align: center; }
            .statement-header h2 { margin-bottom: 4px; font-weight: 600; }
            .statement-subtitle { font-size: 13px; color: #6b7280; }
            .statement-details table { width: 100%; }
            .statement-details td { padding: 4px 0; vertical-align: top; font-size: 13px; }
            .statement-summary { background: #f9fafb; border-radius: 6px; padding: 12px 16px; border: 1px solid #e5e7eb; margin-bottom: 16px; }
            .statement-summary td { font-size: 13px; padding: 4px 0; }
            .statement-summary .label { color: #4b5563; }
            .statement-summary .value { font-weight: 600; }
            .statement-summary .value-success { color: #16a34a; }
            .statement-summary .value-danger { color: #dc2626; }
            .statement-table { width: 100%; border-collapse: collapse; font-size: 13px; }
            .statement-table th, .statement-table td { border: 1px solid #e5e7eb; padding: 8px; }
            .statement-table th { background-color: #f3f4f6; font-weight: 600; }
            .statement-table tfoot th { background-color: #f9fafb; }
            .text-right { text-align: right; }
            .statement-footer { margin-top: 24px; font-size: 12px; color: #6b7280; text-align: right; }
            @media print {
                .no-print, .main-header, .main-sidebar, .content-header { display: none !important; }
                .content-wrapper, .container-full, .statement-wrapper { margin: 0; padding: 0; }
                body { background: #ffffff; }
            }
        </style>

        <div class="row">
            <div class="col-12">
                <div class="statement-wrapper">
                    <div class="statement-card">
                        <div class="statement-header">
                            <h2>Payment Statement</h2>
                            <div class="statement-subtitle">
                                Project: <strong><?php echo htmlspecialchars($customer['project_name']); ?></strong>
                            </div>
                        </div>

                        <div class="statement-details">
                            <table>
                                <tr>
                                    <td width="50%">
                                        <strong>Customer Details</strong><br>
                                        <?php echo htmlspecialchars($customer['name']); ?><br>
                                        <?php echo htmlspecialchars($customer['address']); ?><br>
                                        Phone: <?php echo htmlspecialchars($customer['phone'] ?? $customer['mobile'] ?? ''); ?><br>
                                        Email: <?php echo htmlspecialchars($customer['email']); ?>
                                    </td>
                                    <td width="50%" class="text-right">
                                        <strong>Unit Details</strong><br>
                                        Unit No: <?php echo htmlspecialchars($customer['unit_number']); ?><br>
                                        Area: <?php echo htmlspecialchars($customer['carpet_area']); ?> sq.ft (Carpet)<br>
                                        Booking Date: <?php echo $customer['booking_date'] ? date('d-M-Y', strtotime($customer['booking_date'])) : ''; ?><br>
                                        City: <?php echo htmlspecialchars($customer['city']); ?><br>
                                        State: <?php echo htmlspecialchars($customer['state']); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="statement-summary">
                            <table width="100%">
                                <tr>
                                    <td class="label">Total Deal Amount</td>
                                    <td class="value text-right">₹ <?php echo number_format($customer['total_deal_amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Total Paid</td>
                                    <td class="value value-success text-right">₹ <?php echo number_format($total_paid, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Balance Due</td>
                                    <td class="value value-danger text-right">₹ <?php echo number_format($balance_amount, 2); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="statement-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Particulars</th>
                                        <th>Payment Mode</th>
                                        <th>Receipt No</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payment_list)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No payments found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($payment_list as $p): ?>
                                        <tr>
                                            <td><?php echo $p['payment_date'] ? date('d-M-Y', strtotime($p['payment_date'])) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($p['remarks']); ?></td>
                                            <td><?php echo htmlspecialchars($p['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($p['receipt_no']); ?></td>
                                            <td class="text-right">₹ <?php echo number_format($p['amount'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total</th>
                                        <th class="text-right">₹ <?php echo number_format($total_paid, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="statement-footer">
                            Generated on <?php echo date('d-M-Y H:i'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
