<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access Denied");
}

$customer_id = $_GET['customer_id'] ?? null;
if (!$customer_id) {
    die("Customer ID missing");
}

// Fetch Customer Details
$stmt = $pdo->prepare("SELECT c.*, p.name as project_name, u.unit_number 
                       FROM customers c 
                       LEFT JOIN projects p ON c.project_id = p.id 
                       LEFT JOIN units u ON c.unit_id = u.id 
                       WHERE c.id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    die("Customer not found");
}

// Fetch Payments
$payments = $pdo->prepare("SELECT cp.*, b.bank_name FROM customer_payments cp LEFT JOIN banks b ON cp.bank_id = b.id WHERE customer_id = ? ORDER BY payment_date ASC");
$payments->execute([$customer_id]);
$payment_list = $payments->fetchAll();

$total_paid = 0;
foreach ($payment_list as $p) {
    $total_paid += $p['amount'];
}
$balance_amount = $customer['total_deal_amount'] - $total_paid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Payment Statement</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .details { margin-bottom: 20px; }
        .details table { width: 100%; }
        .details td { padding: 5px; vertical-align: top; }
        .ledger-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .ledger-table th, .ledger-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .ledger-table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment Statement</h2>
            <p><strong>Project:</strong> <?php echo htmlspecialchars($customer['project_name']); ?></p>
        </div>

        <div class="details">
            <table>
                <tr>
                    <td width="50%">
                        <strong>Customer Details:</strong><br>
                        <?php echo htmlspecialchars($customer['name']); ?><br>
                        <?php echo htmlspecialchars($customer['address']); ?><br>
                        Phone: <?php echo htmlspecialchars($customer['mobile']); ?><br>
                        Email: <?php echo htmlspecialchars($customer['email']); ?>
                    </td>
                    <td width="50%" class="text-right">
                        <strong>Unit Details:</strong><br>
                        Unit No: <?php echo htmlspecialchars($customer['unit_number']); ?><br>
                        Area: <?php echo htmlspecialchars($customer['carpet_area']); ?> sq.ft (Carpet)<br>
                        Booking Date: <?php echo date('d-M-Y', strtotime($customer['booking_date'])); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="summary" style="background: #f9f9f9; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd;">
            <table width="100%">
                <tr>
                    <td>Total Deal Amount:</td>
                    <td class="text-right"><strong>₹ <?php echo number_format($customer['total_deal_amount'], 2); ?></strong></td>
                </tr>
                <tr>
                    <td>Total Paid:</td>
                    <td class="text-right text-success"><strong>₹ <?php echo number_format($total_paid, 2); ?></strong></td>
                </tr>
                <tr>
                    <td>Balance Due:</td>
                    <td class="text-right text-danger"><strong>₹ <?php echo number_format($balance_amount, 2); ?></strong></td>
                </tr>
            </table>
        </div>

        <table class="ledger-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th>Mode</th>
                    <th>Receipt No</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payment_list)): ?>
                    <tr><td colspan="5" style="text-align: center;">No payments found.</td></tr>
                <?php else: ?>
                    <?php foreach ($payment_list as $p): ?>
                    <tr>
                        <td><?php echo date('d-M-Y', strtotime($p['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($p['remarks']); ?></td>
                        <td><?php echo htmlspecialchars($p['payment_mode']); ?></td>
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

        <div class="footer">
            <p>Generated on <?php echo date('d-M-Y H:i'); ?></p>
            <button class="no-print" onclick="window.print()">Print Statement</button>
        </div>
    </div>
</body>
</html>
