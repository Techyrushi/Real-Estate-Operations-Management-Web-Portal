<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('view_reports')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Tab Selection
$tab = $_GET['tab'] ?? 'financial';

// 1. Financial Summary Data
$financials = [];
if ($tab == 'financial') {
    $projects = $pdo->query("SELECT id, name FROM projects")->fetchAll();
    foreach ($projects as $p) {
        // Total Sales (Deal Value) - Linked via Units -> Bookings
        $sales = $pdo->prepare("SELECT SUM(b.total_price) 
                               FROM bookings b 
                               JOIN units u ON b.unit_id = u.id 
                               WHERE u.project_id = ? AND b.status != 'Cancelled'");
        $sales->execute([$p['id']]);
        $total_sales = $sales->fetchColumn() ?: 0;

        // Received from Customers - Linked via Payments -> Bookings -> Units
        $received = $pdo->prepare("SELECT SUM(pay.amount) 
                                  FROM payments pay 
                                  JOIN bookings b ON pay.booking_id = b.id 
                                  JOIN units u ON b.unit_id = u.id 
                                  WHERE u.project_id = ?");
        $received->execute([$p['id']]);
        $total_received = $received->fetchColumn() ?: 0;

        // Expenses
        $expenses = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE project_id = ?");
        $expenses->execute([$p['id']]);
        $total_expenses = $expenses->fetchColumn() ?: 0;

        $financials[] = [
            'project' => $p['name'],
            'sales' => $total_sales,
            'received' => $total_received,
            'expenses' => $total_expenses,
            'balance' => $total_received - $total_expenses
        ];
    }
}

// 2. Partner Capital Data
$partners_data = [];
if ($tab == 'partners') {
    $partners = $pdo->query("SELECT * FROM partners")->fetchAll();
    foreach ($partners as $p) {
        $credits = $pdo->prepare("SELECT SUM(amount) FROM partner_ledger WHERE partner_id = ? AND type = 'Credit'");
        $credits->execute([$p['id']]);
        $total_credit = $credits->fetchColumn() ?: 0;

        $debits = $pdo->prepare("SELECT SUM(amount) FROM partner_ledger WHERE partner_id = ? AND type = 'Debit'");
        $debits->execute([$p['id']]);
        $total_debit = $debits->fetchColumn() ?: 0;

        $current_capital = $p['opening_capital'] + $total_credit - $total_debit;
        
        $partners_data[] = [
            'name' => $p['name'],
            'opening' => $p['opening_capital'],
            'invested' => $total_credit,
            'withdrawn' => $total_debit,
            'current' => $current_capital
        ];
    }
}

// 3. Outstanding Data
$outstanding_data = [];
if ($tab == 'outstanding') {
    // Correct query linking Customers -> Bookings -> Units -> Projects
    $sql = "SELECT c.id as customer_id, c.name, p.name as project_name, b.total_price as deal_value, b.id as booking_id 
            FROM customers c 
            JOIN bookings b ON c.id = b.customer_id 
            JOIN units u ON b.unit_id = u.id 
            JOIN projects p ON u.project_id = p.id 
            WHERE b.status != 'Cancelled'";
    
    $customers = $pdo->query($sql)->fetchAll();
    
    foreach ($customers as $c) {
        $paid = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE booking_id = ?");
        $paid->execute([$c['booking_id']]);
        $total_paid = $paid->fetchColumn() ?: 0;

        $balance = $c['deal_value'] - $total_paid;
        
        if ($balance > 0) {
            $outstanding_data[] = [
                'id' => $c['customer_id'],
                'name' => $c['name'],
                'project' => $c['project_name'],
                'deal_value' => $c['deal_value'],
                'paid' => $total_paid,
                'balance' => $balance
            ];
        }
    }
    // Sort by Balance DESC
    usort($outstanding_data, function($a, $b) { return $b['balance'] <=> $a['balance']; });
}

// 4. Cash Flow Data
$cashflow_data = [];
if ($tab == 'cashflow') {
    $sql = "SELECT 'Sales' as category, p.payment_date as date, 
                   CONCAT('Payment from ', c.name, ' (Unit ', u.unit_number, ')') as description,
                   p.amount as inflow, 0 as outflow, p.payment_method as mode
            FROM payments p
            JOIN bookings b ON p.booking_id = b.id
            JOIN customers c ON b.customer_id = c.id
            JOIN units u ON b.unit_id = u.id

            UNION ALL

            SELECT 'Expense' as category, e.expense_date as date,
                   CONCAT(IFNULL(e.category, 'Expense'), ': ', IFNULL(e.description, '')) as description,
                   0 as inflow, e.amount as outflow, 'Cash/Bank' as mode
            FROM expenses e
            
            UNION ALL

            SELECT 'Partner Capital' as category, pl.transaction_date as date,
                   CONCAT('Partner: ', ptr.name, ' - ', pl.remarks) as description,
                   CASE WHEN pl.type = 'Credit' THEN pl.amount ELSE 0 END as inflow,
                   CASE WHEN pl.type = 'Debit' THEN pl.amount ELSE 0 END as outflow,
                   pl.mode as mode
            FROM partner_ledger pl
            JOIN partners ptr ON pl.partner_id = ptr.id

            ORDER BY date DESC";
            
    $cashflow_data = $pdo->query($sql)->fetchAll();
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Reports & Analytics</h4>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li><a href="?tab=financial" class="<?php echo $tab == 'financial' ? 'active' : ''; ?>">Financial Summary</a></li>
                        <li><a href="?tab=partners" class="<?php echo $tab == 'partners' ? 'active' : ''; ?>">Partner Capital</a></li>
                        <li><a href="?tab=outstanding" class="<?php echo $tab == 'outstanding' ? 'active' : ''; ?>">Outstanding Receivables</a></li>
                        <li><a href="?tab=cashflow" class="<?php echo $tab == 'cashflow' ? 'active' : ''; ?>">Cash Flow</a></li>
                    </ul>

                    <div class="tab-content">
                        <!-- Financial Summary Tab -->
                        <div class="tab-pane <?php echo $tab == 'financial' ? 'active' : ''; ?>" id="financial">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>Project</th>
                                            <th class="text-end">Total Deal Value</th>
                                            <th class="text-end">Collections (Inflow)</th>
                                            <th class="text-end">Expenses (Outflow)</th>
                                            <th class="text-end">Net Cash Flow</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($financials)): foreach ($financials as $row): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row['project']); ?></strong></td>
                                            <td class="text-end">₹ <?php echo number_format($row['sales'], 2); ?></td>
                                            <td class="text-end text-success">₹ <?php echo number_format($row['received'], 2); ?></td>
                                            <td class="text-end text-danger">₹ <?php echo number_format($row['expenses'], 2); ?></td>
                                            <td class="text-end fw-bold <?php echo $row['balance'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                ₹ <?php echo number_format($row['balance'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="5" class="text-center">No financial data available.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Partner Capital Tab -->
                        <div class="tab-pane <?php echo $tab == 'partners' ? 'active' : ''; ?>" id="partners">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Partner Name</th>
                                            <th class="text-end">Opening Capital</th>
                                            <th class="text-end">Addl. Invested</th>
                                            <th class="text-end">Withdrawn</th>
                                            <th class="text-end">Current Capital</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($partners_data)): foreach ($partners_data as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td class="text-end">₹ <?php echo number_format($row['opening'], 2); ?></td>
                                            <td class="text-end text-success">₹ <?php echo number_format($row['invested'], 2); ?></td>
                                            <td class="text-end text-danger">₹ <?php echo number_format($row['withdrawn'], 2); ?></td>
                                            <td class="text-end fw-bold">₹ <?php echo number_format($row['current'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="5" class="text-center">No partner data available.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Outstanding Tab -->
                        <div class="tab-pane <?php echo $tab == 'outstanding' ? 'active' : ''; ?>" id="outstanding">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Project</th>
                                            <th class="text-end">Total Deal</th>
                                            <th class="text-end">Paid Amount</th>
                                            <th class="text-end">Balance Due</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($outstanding_data)): foreach ($outstanding_data as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['project']); ?></td>
                                            <td class="text-end">₹ <?php echo number_format($row['deal_value'], 2); ?></td>
                                            <td class="text-end text-success">₹ <?php echo number_format($row['paid'], 2); ?></td>
                                            <td class="text-end text-danger fw-bold">₹ <?php echo number_format($row['balance'], 2); ?></td>
                                            <td><a href="admin_customer_ledger.php?customer_id=<?php echo $row['id']; ?>" class="btn btn-xs btn-warning">View</a></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="6" class="text-center">No outstanding payments.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Cash Flow Tab -->
                        <div class="tab-pane <?php echo $tab == 'cashflow' ? 'active' : ''; ?>" id="cashflow">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th>Mode</th>
                                            <th class="text-end">Inflow (+)</th>
                                            <th class="text-end">Outflow (-)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_in = 0;
                                        $total_out = 0;
                                        if(!empty($cashflow_data)): foreach ($cashflow_data as $row): 
                                            $total_in += $row['inflow'];
                                            $total_out += $row['outflow'];
                                        ?>
                                        <tr>
                                            <td><?php echo date('d-M-Y', strtotime($row['date'])); ?></td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo match($row['category']) {
                                                        'Sales' => 'badge-success',
                                                        'Expense' => 'badge-danger',
                                                        'Partner Capital' => 'badge-info',
                                                        default => 'badge-secondary'
                                                    };
                                                ?>"><?php echo $row['category']; ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td><?php echo htmlspecialchars($row['mode']); ?></td>
                                            <td class="text-end text-success"><?php echo $row['inflow'] > 0 ? '₹ '.number_format($row['inflow'], 2) : '-'; ?></td>
                                            <td class="text-end text-danger"><?php echo $row['outflow'] > 0 ? '₹ '.number_format($row['outflow'], 2) : '-'; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="fw-bold bg-light">
                                            <td colspan="4" class="text-end">TOTAL</td>
                                            <td class="text-end text-success">₹ <?php echo number_format($total_in, 2); ?></td>
                                            <td class="text-end text-danger">₹ <?php echo number_format($total_out, 2); ?></td>
                                        </tr>
                                        <tr class="fw-bold bg-secondary text-white">
                                            <td colspan="4" class="text-end">NET CASH FLOW</td>
                                            <td colspan="2" class="text-center">₹ <?php echo number_format($total_in - $total_out, 2); ?></td>
                                        </tr>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="text-center">No cash flow data available.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
