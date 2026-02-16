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

// Global Filters
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$project_filter = $_GET['project_id'] ?? '';
$vendor_filter = $_GET['vendor_id'] ?? '';

$project_options = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$vendor_options = $pdo->query("SELECT id, name FROM vendors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$filter_params = [];
if ($from_date !== '') {
    $filter_params['from_date'] = $from_date;
}
if ($to_date !== '') {
    $filter_params['to_date'] = $to_date;
}
if ($project_filter !== '') {
    $filter_params['project_id'] = $project_filter;
}
if ($vendor_filter !== '') {
    $filter_params['vendor_id'] = $vendor_filter;
}

$tab_urls = [
    'financial' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'financial'])),
    'partners' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'partners'])),
    'outstanding' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'outstanding'])),
    'aging' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'aging'])),
    'expenses' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'expenses'])),
    'vendor90' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'vendor90'])),
    'cashflow' => 'admin_reports.php?' . http_build_query(array_merge($filter_params, ['tab' => 'cashflow'])),
];

// 1. Financial Summary Data
$financials = [];
if ($tab == 'financial') {
    $projects = $project_options;
    foreach ($projects as $p) {
        if ($project_filter !== '' && (int)$project_filter !== (int)$p['id']) {
            continue;
        }

        $sales_sql = "SELECT SUM(b.total_price) 
                       FROM bookings b 
                       JOIN units u ON b.unit_id = u.id 
                       WHERE u.project_id = ? AND b.status != 'Cancelled'";
        $sales_params = [$p['id']];
        if ($from_date !== '') {
            $sales_sql .= " AND b.booking_date >= ?";
            $sales_params[] = $from_date;
        }
        if ($to_date !== '') {
            $sales_sql .= " AND b.booking_date <= ?";
            $sales_params[] = $to_date;
        }
        $sales = $pdo->prepare($sales_sql);
        $sales->execute($sales_params);
        $total_sales = $sales->fetchColumn() ?: 0;

        $rec_sql = "SELECT SUM(pay.amount) 
                                  FROM payments pay 
                                  JOIN bookings b ON pay.booking_id = b.id 
                                  JOIN units u ON b.unit_id = u.id 
                                  WHERE u.project_id = ?";
        $rec_params = [$p['id']];
        if ($from_date !== '') {
            $rec_sql .= " AND pay.payment_date >= ?";
            $rec_params[] = $from_date;
        }
        if ($to_date !== '') {
            $rec_sql .= " AND pay.payment_date <= ?";
            $rec_params[] = $to_date;
        }
        $received = $pdo->prepare($rec_sql);
        $received->execute($rec_params);
        $total_received = $received->fetchColumn() ?: 0;

        $exp_sql = "SELECT SUM(amount) FROM expenses WHERE project_id = ?";
        $exp_params = [$p['id']];
        if ($from_date !== '') {
            $exp_sql .= " AND expense_date >= ?";
            $exp_params[] = $from_date;
        }
        if ($to_date !== '') {
            $exp_sql .= " AND expense_date <= ?";
            $exp_params[] = $to_date;
        }
        $expenses_stmt = $pdo->prepare($exp_sql);
        $expenses_stmt->execute($exp_params);
        $total_expenses = $expenses_stmt->fetchColumn() ?: 0;

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
    $sql = "SELECT c.id as customer_id, c.name, p.id as project_id, p.name as project_name, b.total_price as deal_value, b.id as booking_id, b.booking_date 
            FROM customers c 
            JOIN bookings b ON c.id = b.customer_id 
            JOIN units u ON b.unit_id = u.id 
            JOIN projects p ON u.project_id = p.id 
            WHERE b.status != 'Cancelled'";

    $params = [];
    if ($project_filter !== '') {
        $sql .= " AND p.id = ?";
        $params[] = $project_filter;
    }
    if ($from_date !== '') {
        $sql .= " AND b.booking_date >= ?";
        $params[] = $from_date;
    }
    if ($to_date !== '') {
        $sql .= " AND b.booking_date <= ?";
        $params[] = $to_date;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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

// Aging Data
$aging_data = [];
if ($tab == 'aging') {
    $sql = "SELECT c.id as customer_id, c.name, p.id as project_id, p.name as project_name, b.total_price as deal_value, b.id as booking_id, b.booking_date 
            FROM customers c 
            JOIN bookings b ON c.id = b.customer_id 
            JOIN units u ON b.unit_id = u.id 
            JOIN projects p ON u.project_id = p.id 
            WHERE b.status != 'Cancelled'";

    $params = [];
    if ($project_filter !== '') {
        $sql .= " AND p.id = ?";
        $params[] = $project_filter;
    }
    if ($from_date !== '') {
        $sql .= " AND b.booking_date >= ?";
        $params[] = $from_date;
    }
    if ($to_date !== '') {
        $sql .= " AND b.booking_date <= ?";
        $params[] = $to_date;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $today = date('Y-m-d');
    foreach ($customers as $c) {
        $paid = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE booking_id = ?");
        $paid->execute([$c['booking_id']]);
        $total_paid = $paid->fetchColumn() ?: 0;

        $balance = $c['deal_value'] - $total_paid;
        
        if ($balance > 0) {
            $days = (strtotime($today) - strtotime($c['booking_date'])) / 86400;
            $days = (int)max(0, $days);
            if ($days <= 30) {
                $bucket = '0-30';
            } elseif ($days <= 60) {
                $bucket = '31-60';
            } elseif ($days <= 90) {
                $bucket = '61-90';
            } else {
                $bucket = '90+';
            }

            $aging_data[] = [
                'id' => $c['customer_id'],
                'name' => $c['name'],
                'project' => $c['project_name'],
                'deal_value' => $c['deal_value'],
                'paid' => $total_paid,
                'balance' => $balance,
                'booking_date' => $c['booking_date'],
                'days' => $days,
                'bucket' => $bucket
            ];
        }
    }
}

// Expense Ledger
$expenses_data = [];
if ($tab == 'expenses') {
    $sql = "SELECT e.*, 
                   p.name as project_name, 
                   v.name as vendor_name, 
                   m.category as material_name, 
                   b.bank_name 
            FROM expenses e
            LEFT JOIN projects p ON e.project_id = p.id
            LEFT JOIN vendors v ON e.vendor_id = v.id
            LEFT JOIN materials m ON e.material_id = m.id
            LEFT JOIN banks b ON e.bank_id = b.id
            WHERE 1=1";

    $params = [];
    if ($project_filter !== '') {
        $sql .= " AND e.project_id = ?";
        $params[] = $project_filter;
    }
    if ($vendor_filter !== '') {
        $sql .= " AND e.vendor_id = ?";
        $params[] = $vendor_filter;
    }
    if ($from_date !== '') {
        $sql .= " AND e.expense_date >= ?";
        $params[] = $from_date;
    }
    if ($to_date !== '') {
        $sql .= " AND e.expense_date <= ?";
        $params[] = $to_date;
    }

    $sql .= " ORDER BY e.expense_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $expenses_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Vendor 90-day Activity
$vendor_activity = [];
if ($tab == 'vendor90') {
    $window_start = date('Y-m-d', strtotime('-90 days'));
    $sql = "SELECT v.id, v.name,
                   SUM(IFNULL(e.amount,0) + IFNULL(e.gst_amount,0)) as total_spent,
                   COUNT(e.id) as bills_count,
                   MIN(e.expense_date) as first_txn,
                   MAX(e.expense_date) as last_txn
            FROM vendors v
            LEFT JOIN expenses e ON e.vendor_id = v.id AND e.expense_date >= ?";
    $params = [$window_start];
    if ($project_filter !== '') {
        $sql .= " AND e.project_id = ?";
        $params[] = $project_filter;
    }
    if ($vendor_filter !== '') {
        $sql .= " AND v.id = ?";
        $params[] = $vendor_filter;
    }
    $sql .= " GROUP BY v.id, v.name ORDER BY total_spent DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $vendor_activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 4. Cash Flow Data
$cashflow_data = [];
if ($tab == 'cashflow') {
    $sql = "SELECT 'Sales' as category, p.payment_date as date, 
                   CONCAT('Payment from ', c.name, ' (Unit ', IFNULL(u.flat_no, ''), ')') as description,
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

$financial_summary = [
    'total_sales' => 0,
    'total_received' => 0,
    'total_expenses' => 0,
    'total_balance' => 0,
];
if (!empty($financials)) {
    foreach ($financials as $row) {
        $financial_summary['total_sales'] += (float)$row['sales'];
        $financial_summary['total_received'] += (float)$row['received'];
        $financial_summary['total_expenses'] += (float)$row['expenses'];
        $financial_summary['total_balance'] += (float)$row['balance'];
    }
}

$partners_summary = [
    'count' => 0,
    'total_opening' => 0,
    'total_current' => 0,
];
if (!empty($partners_data)) {
    $partners_summary['count'] = count($partners_data);
    foreach ($partners_data as $row) {
        $partners_summary['total_opening'] += (float)$row['opening'];
        $partners_summary['total_current'] += (float)$row['current'];
    }
}

$outstanding_summary = [
    'customers' => 0,
    'total_balance' => 0,
];
if (!empty($outstanding_data)) {
    foreach ($outstanding_data as $row) {
        if ($row['balance'] > 0) {
            $outstanding_summary['customers']++;
            $outstanding_summary['total_balance'] += (float)$row['balance'];
        }
    }
}

$aging_buckets_summary = [
    '0-30' => 0,
    '31-60' => 0,
    '61-90' => 0,
    '90+' => 0,
];
if (!empty($aging_data)) {
    foreach ($aging_data as $row) {
        $bucket = $row['bucket'];
        if (!isset($aging_buckets_summary[$bucket])) {
            $aging_buckets_summary[$bucket] = 0;
        }
        $aging_buckets_summary[$bucket] += (float)$row['balance'];
    }
}

$expenses_summary = [
    'count' => 0,
    'total_amount' => 0,
    'total_gst' => 0,
    'total_total' => 0,
];
if (!empty($expenses_data)) {
    $expenses_summary['count'] = count($expenses_data);
    foreach ($expenses_data as $row) {
        $amount = (float)($row['amount'] ?? 0);
        $gst = (float)($row['gst_amount'] ?? 0);
        $expenses_summary['total_amount'] += $amount;
        $expenses_summary['total_gst'] += $gst;
        $expenses_summary['total_total'] += $amount + $gst;
    }
}

$vendor_summary = [
    'vendors' => 0,
    'total_spent' => 0,
];
if (!empty($vendor_activity)) {
    $vendor_summary['vendors'] = count($vendor_activity);
    foreach ($vendor_activity as $row) {
        $vendor_summary['total_spent'] += (float)$row['total_spent'];
    }
}

$cashflow_summary = [
    'total_in' => 0,
    'total_out' => 0,
];
if (!empty($cashflow_data)) {
    foreach ($cashflow_data as $row) {
        $cashflow_summary['total_in'] += (float)$row['inflow'];
        $cashflow_summary['total_out'] += (float)$row['outflow'];
    }
}

if (isset($_GET['export'])) {
    $export = $_GET['export'];
    if ($export === 'financial_excel' && !empty($financials)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="project_financials_' . date('Ymd') . '.xls"');
        echo "<table border='1'>";
        echo "<tr><th>Project</th><th>Total Deal</th><th>Collections</th><th>Expenses</th><th>Net Cash Flow</th></tr>";
        foreach ($financials as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['project']) . "</td>";
            echo "<td>" . number_format($row['sales'], 2) . "</td>";
            echo "<td>" . number_format($row['received'], 2) . "</td>";
            echo "<td>" . number_format($row['expenses'], 2) . "</td>";
            echo "<td>" . number_format($row['balance'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
    if ($export === 'aging_excel' && !empty($aging_data)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="receivables_aging_' . date('Ymd') . '.xls"');
        echo "<table border='1'>";
        echo "<tr><th>Customer</th><th>Project</th><th>Booking Date</th><th>Total Deal</th><th>Paid</th><th>Balance</th><th>Days</th><th>Bucket</th></tr>";
        foreach ($aging_data as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['project']) . "</td>";
            echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
            echo "<td>" . number_format($row['deal_value'], 2) . "</td>";
            echo "<td>" . number_format($row['paid'], 2) . "</td>";
            echo "<td>" . number_format($row['balance'], 2) . "</td>";
            echo "<td>" . (int)$row['days'] . "</td>";
            echo "<td>" . htmlspecialchars($row['bucket']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
    if ($export === 'expenses_excel' && !empty($expenses_data)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="expense_ledger_' . date('Ymd') . '.xls"');
        echo "<table border='1'>";
        echo "<tr><th>Date</th><th>Project</th><th>Vendor</th><th>Material</th><th>Amount</th><th>GST</th><th>Total</th><th>Mode</th><th>Reference</th><th>Remarks</th></tr>";
        foreach ($expenses_data as $row) {
            $total = ($row['amount'] ?? 0) + ($row['gst_amount'] ?? 0);
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['expense_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['project_name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['vendor_name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['material_name'] ?? '') . "</td>";
            echo "<td>" . number_format($row['amount'], 2) . "</td>";
            echo "<td>" . number_format($row['gst_amount'], 2) . "</td>";
            echo "<td>" . number_format($total, 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['payment_mode'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['reference_no'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['remarks'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
    if ($export === 'vendor90_excel' && !empty($vendor_activity)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="vendor_90day_activity_' . date('Ymd') . '.xls"');
        echo "<table border='1'>";
        echo "<tr><th>Vendor</th><th>Total Spent</th><th>Bills Count</th><th>First Txn</th><th>Last Txn</th></tr>";
        foreach ($vendor_activity as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . number_format($row['total_spent'], 2) . "</td>";
            echo "<td>" . (int)$row['bills_count'] . "</td>";
            echo "<td>" . htmlspecialchars($row['first_txn']) . "</td>";
            echo "<td>" . htmlspecialchars($row['last_txn']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}

$export_financial_query = http_build_query(array_merge($filter_params, ['tab' => 'financial', 'export' => 'financial_excel']));
$export_aging_query = http_build_query(array_merge($filter_params, ['tab' => 'aging', 'export' => 'aging_excel']));
$export_expenses_query = http_build_query(array_merge($filter_params, ['tab' => 'expenses', 'export' => 'expenses_excel']));
$export_vendor90_query = http_build_query(array_merge($filter_params, ['tab' => 'vendor90', 'export' => 'vendor90_excel']));
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
        <div class="row mb-3">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <form method="GET" action="admin_reports.php" class="row g-2 align-items-end">
                            <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Project</label>
                                <select name="project_id" class="form-select">
                                    <option value="">All Projects</option>
                                    <?php foreach ($project_options as $p): ?>
                                        <option value="<?php echo $p['id']; ?>" <?php echo $project_filter == $p['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vendor (for expense/vendor reports)</label>
                                <select name="vendor_id" class="form-select">
                                    <option value="">All Vendors</option>
                                    <?php foreach ($vendor_options as $v): ?>
                                        <option value="<?php echo $v['id']; ?>" <?php echo $vendor_filter == $v['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($v['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Search</label>
                                <input type="text" id="reportSearch" class="form-control" placeholder="Search in table">
                            </div>
                            <div class="col-md-12 mt-2 d-flex justify-content-between">
                                <div class="text-muted small">
                                    Filters apply to all relevant tabs. Search works on the active table.
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2"><i class="ti-search"></i> Apply</button>
                                    <a href="<?php echo htmlspecialchars('admin_reports.php?tab=' . urlencode($tab)); ?>" class="btn btn-secondary"><i class="ti-reload"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li><a href="<?php echo htmlspecialchars($tab_urls['financial']); ?>" class="<?php echo $tab == 'financial' ? 'active' : ''; ?>">Financial Summary</a></li>
                        <li><a href="<?php echo htmlspecialchars($tab_urls['partners']); ?>" class="<?php echo $tab == 'partners' ? 'active' : ''; ?>">Partner Capital</a></li>
                        <li><a href="<?php echo htmlspecialchars($tab_urls['outstanding']); ?>" class="<?php echo $tab == 'outstanding' ? 'active' : ''; ?>">Outstanding Receivables</a></li>
                        <li><a href="<?php echo htmlspecialchars($tab_urls['aging']); ?>" class="<?php echo $tab == 'aging' ? 'active' : ''; ?>">Receivables Aging</a></li>
                        <li><a href="<?php echo htmlspecialchars($tab_urls['expenses']); ?>" class="<?php echo $tab == 'expenses' ? 'active' : ''; ?>">Expense Ledger</a></li>
                        <li><a href="<?php echo htmlspecialchars($tab_urls['vendor90']); ?>" class="<?php echo $tab == 'vendor90' ? 'active' : ''; ?>">Vendor Activity</a></li>
                        <li><a href="<?php echo htmlspecialchars($tab_urls['cashflow']); ?>" class="<?php echo $tab == 'cashflow' ? 'active' : ''; ?>">Cash Flow</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane <?php echo $tab == 'financial' ? 'active' : ''; ?>" id="financial">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Project-wise Financial Summary</h5>
                                <div>
                                    <?php if (!empty($financials)): ?>
                                        <button type="button" class="btn btn-secondary btn-sm me-2" onclick="printReportSection('financial')">
                                            <i class="ti-printer"></i> Print / PDF
                                        </button>
                                        <a href="admin_reports.php?<?php echo htmlspecialchars($export_financial_query); ?>" class="btn btn-success btn-sm">
                                            <i class="ti-download"></i> Export Excel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div id="financial_bar_chart" style="height: 320px;"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Deal Value</div>
                                        <div class="h5 mb-0">₹ <?php echo number_format($financial_summary['total_sales'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Collections</div>
                                        <div class="h5 mb-0">₹ <?php echo number_format($financial_summary['total_received'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Expenses</div>
                                        <div class="h5 mb-0">₹ <?php echo number_format($financial_summary['total_expenses'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Net Cash Flow</div>
                                        <div class="h5 mb-0 <?php echo $financial_summary['total_balance'] >= 0 ? 'text-success' : 'text-danger'; ?>">₹ <?php echo number_format($financial_summary['total_balance'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover report-table">
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
                                        <?php if (!empty($financials)): foreach ($financials as $row): ?>
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

                        <div class="tab-pane <?php echo $tab == 'partners' ? 'active' : ''; ?>" id="partners">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Partner Capital & Share</h5>
                                <?php if (!empty($partners_data)): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="printReportSection('partners')">
                                        <i class="ti-printer"></i> Print / PDF
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Partners</div>
                                        <div class="h5 mb-0"><?php echo (int)$partners_summary['count']; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Opening Capital</div>
                                        <div class="h5 mb-0">₹ <?php echo number_format($partners_summary['total_opening'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Current Capital</div>
                                        <div class="h5 mb-0">₹ <?php echo number_format($partners_summary['total_current'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped report-table">
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
                                        <?php if (!empty($partners_data)): foreach ($partners_data as $row): ?>
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

                        <div class="tab-pane <?php echo $tab == 'outstanding' ? 'active' : ''; ?>" id="outstanding">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Outstanding Receivables</h5>
                                <?php if (!empty($outstanding_data)): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="printReportSection('outstanding')">
                                        <i class="ti-printer"></i> Print / PDF
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Customers With Outstanding</div>
                                        <div class="h5 mb-0"><?php echo (int)$outstanding_summary['customers']; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Outstanding Amount</div>
                                        <div class="h5 mb-0 text-danger">₹ <?php echo number_format($outstanding_summary['total_balance'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover report-table">
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
                                        <?php if (!empty($outstanding_data)): foreach ($outstanding_data as $row): ?>
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

                        <div class="tab-pane <?php echo $tab == 'aging' ? 'active' : ''; ?>" id="aging">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Customer Receivables Aging</h5>
                                <div>
                                    <?php if (!empty($aging_data)): ?>
                                        <button type="button" class="btn btn-secondary btn-sm me-2" onclick="printReportSection('aging')">
                                            <i class="ti-printer"></i> Print / PDF
                                        </button>
                                        <a href="admin_reports.php?<?php echo htmlspecialchars($export_aging_query); ?>" class="btn btn-success btn-sm">
                                            <i class="ti-download"></i> Export Excel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div id="aging_bar_chart" style="height: 320px;"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">0-30 Days</div>
                                        <div class="h6 mb-0 text-danger">₹ <?php echo number_format($aging_buckets_summary['0-30'] ?? 0, 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">31-60 Days</div>
                                        <div class="h6 mb-0 text-danger">₹ <?php echo number_format($aging_buckets_summary['31-60'] ?? 0, 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">61-90 Days</div>
                                        <div class="h6 mb-0 text-danger">₹ <?php echo number_format($aging_buckets_summary['61-90'] ?? 0, 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">90+ Days</div>
                                        <div class="h6 mb-0 text-danger">₹ <?php echo number_format($aging_buckets_summary['90+'] ?? 0, 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover report-table">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Project</th>
                                            <th>Booking Date</th>
                                            <th class="text-end">Total Deal</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Balance</th>
                                            <th class="text-end">Days</th>
                                            <th>Aging Bucket</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($aging_data)): foreach ($aging_data as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['project']); ?></td>
                                            <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                            <td class="text-end">₹ <?php echo number_format($row['deal_value'], 2); ?></td>
                                            <td class="text-end text-success">₹ <?php echo number_format($row['paid'], 2); ?></td>
                                            <td class="text-end text-danger fw-bold">₹ <?php echo number_format($row['balance'], 2); ?></td>
                                            <td class="text-end"><?php echo (int)$row['days']; ?></td>
                                            <td><span class="badge bg-info"><?php echo htmlspecialchars($row['bucket']); ?></span></td>
                                            <td><a href="admin_customer_ledger.php?customer_id=<?php echo $row['id']; ?>" class="btn btn-xs btn-warning">View</a></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="9" class="text-center">No aging data available.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane <?php echo $tab == 'expenses' ? 'active' : ''; ?>" id="expenses">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Expense Ledger (Project / Vendor)</h5>
                                <div>
                                    <?php if (!empty($expenses_data)): ?>
                                        <button type="button" class="btn btn-secondary btn-sm me-2" onclick="printReportSection('expenses')">
                                            <i class="ti-printer"></i> Print / PDF
                                        </button>
                                        <a href="admin_reports.php?<?php echo htmlspecialchars($export_expenses_query); ?>" class="btn btn-success btn-sm">
                                            <i class="ti-download"></i> Export Excel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Bills</div>
                                        <div class="h6 mb-0"><?php echo (int)$expenses_summary['count']; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Amount</div>
                                        <div class="h6 mb-0">₹ <?php echo number_format($expenses_summary['total_amount'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">GST</div>
                                        <div class="h6 mb-0">₹ <?php echo number_format($expenses_summary['total_gst'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total</div>
                                        <div class="h6 mb-0">₹ <?php echo number_format($expenses_summary['total_total'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover report-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Project</th>
                                            <th>Vendor</th>
                                            <th>Material</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">GST</th>
                                            <th class="text-end">Total</th>
                                            <th>Mode</th>
                                            <th>Reference</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($expenses_data)): foreach ($expenses_data as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['expense_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['project_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['vendor_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['material_name'] ?? ''); ?></td>
                                            <td class="text-end">₹ <?php echo number_format($row['amount'], 2); ?></td>
                                            <td class="text-end">₹ <?php echo number_format($row['gst_amount'], 2); ?></td>
                                            <td class="text-end">
                                                <?php 
                                                $total = ($row['amount'] ?? 0) + ($row['gst_amount'] ?? 0);
                                                echo '₹ ' . number_format($total, 2);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['payment_mode'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['reference_no'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="10" class="text-center">No expense data available.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane <?php echo $tab == 'vendor90' ? 'active' : ''; ?>" id="vendor90">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Vendor 90-day Activity</h5>
                                <div>
                                    <?php if (!empty($vendor_activity)): ?>
                                        <button type="button" class="btn btn-secondary btn-sm me-2" onclick="printReportSection('vendor90')">
                                            <i class="ti-printer"></i> Print / PDF
                                        </button>
                                        <a href="admin_reports.php?<?php echo htmlspecialchars($export_vendor90_query); ?>" class="btn btn-success btn-sm">
                                            <i class="ti-download"></i> Export Excel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div id="vendor_spend_chart" style="height: 320px;"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Active Vendors</div>
                                        <div class="h6 mb-0"><?php echo (int)$vendor_summary['vendors']; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Spent (90 days)</div>
                                        <div class="h6 mb-0">₹ <?php echo number_format($vendor_summary['total_spent'], 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover report-table">
                                    <thead>
                                        <tr>
                                            <th>Vendor</th>
                                            <th class="text-end">Total Spent</th>
                                            <th class="text-end">Bills Count</th>
                                            <th>First Transaction</th>
                                            <th>Last Transaction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vendor_activity)): foreach ($vendor_activity as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td class="text-end">₹ <?php echo number_format($row['total_spent'], 2); ?></td>
                                            <td class="text-end"><?php echo (int)$row['bills_count']; ?></td>
                                            <td><?php echo htmlspecialchars($row['first_txn']); ?></td>
                                            <td><?php echo htmlspecialchars($row['last_txn']); ?></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="5" class="text-center">No vendor activity in the last 90 days.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane <?php echo $tab == 'cashflow' ? 'active' : ''; ?>" id="cashflow">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Cash Flow Statement</h5>
                                <?php if (!empty($cashflow_data)): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="printReportSection('cashflow')">
                                        <i class="ti-printer"></i> Print / PDF
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div id="cashflow_line_chart" style="height: 320px;"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Inflow</div>
                                        <div class="h6 mb-0 text-success">₹ <?php echo number_format($cashflow_summary['total_in'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Total Outflow</div>
                                        <div class="h6 mb-0 text-danger">₹ <?php echo number_format($cashflow_summary['total_out'], 2); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <div class="text-muted small">Net Cash Flow</div>
                                        <?php $net_cf = $cashflow_summary['total_in'] - $cashflow_summary['total_out']; ?>
                                        <div class="h6 mb-0 <?php echo $net_cf >= 0 ? 'text-success' : 'text-danger'; ?>">₹ <?php echo number_format($net_cf, 2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover report-table">
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
                                        if (!empty($cashflow_data)): foreach ($cashflow_data as $row): 
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

<?php
$hide_dashboard_js = true;

$financial_labels_json = '[]';
$financial_sales_json = '[]';
$financial_received_json = '[]';
$financial_expenses_json = '[]';

if (!empty($financials)) {
    $financial_labels = [];
    $financial_sales_data = [];
    $financial_received_data = [];
    $financial_expenses_data = [];
    foreach ($financials as $row) {
        $financial_labels[] = $row['project'];
        $financial_sales_data[] = (float)$row['sales'];
        $financial_received_data[] = (float)$row['received'];
        $financial_expenses_data[] = (float)$row['expenses'];
    }
    $financial_labels_json = json_encode($financial_labels);
    $financial_sales_json = json_encode($financial_sales_data);
    $financial_received_json = json_encode($financial_received_data);
    $financial_expenses_json = json_encode($financial_expenses_data);
}

$aging_bucket_labels_json = '[]';
$aging_bucket_values_json = '[]';
if (!empty($aging_buckets_summary)) {
    $aging_bucket_labels_json = json_encode(array_keys($aging_buckets_summary));
    $aging_bucket_values_json = json_encode(array_values($aging_buckets_summary));
}

$vendor_labels_json = '[]';
$vendor_values_json = '[]';
if (!empty($vendor_activity)) {
    $vendor_labels = [];
    $vendor_values = [];
    $count = 0;
    foreach ($vendor_activity as $row) {
        $vendor_labels[] = $row['name'];
        $vendor_values[] = (float)$row['total_spent'];
        $count++;
        if ($count >= 8) {
            break;
        }
    }
    $vendor_labels_json = json_encode($vendor_labels);
    $vendor_values_json = json_encode($vendor_values);
}

$cashflow_dates_json = '[]';
$cashflow_inflow_json = '[]';
$cashflow_outflow_json = '[]';
$cashflow_net_json = '[]';
if (!empty($cashflow_data)) {
    $daily = [];
    foreach ($cashflow_data as $row) {
        $dateKey = $row['date'];
        if (!isset($daily[$dateKey])) {
            $daily[$dateKey] = [
                'in' => 0,
                'out' => 0,
            ];
        }
        $daily[$dateKey]['in'] += (float)$row['inflow'];
        $daily[$dateKey]['out'] += (float)$row['outflow'];
    }
    ksort($daily);
    $dates = [];
    $inflowSeries = [];
    $outflowSeries = [];
    $netSeries = [];
    foreach ($daily as $dateKey => $vals) {
        $dates[] = date('d-M', strtotime($dateKey));
        $inflowSeries[] = $vals['in'];
        $outflowSeries[] = $vals['out'];
        $netSeries[] = $vals['in'] - $vals['out'];
    }
    $cashflow_dates_json = json_encode($dates);
    $cashflow_inflow_json = json_encode($inflowSeries);
    $cashflow_outflow_json = json_encode($outflowSeries);
    $cashflow_net_json = json_encode($netSeries);
}

$extra_js = <<<EOT
<script>
$(function () {
    'use strict';

    window.printReportSection = function (tabId) {
        var pane = $('.tab-content .tab-pane#' + tabId);
        if (!pane.length) {
            return;
        }
        var printWindow = window.open('', '_blank');
        var html = '<html><head><title>Report</title>';
        html += '<style>body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;padding:16px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #e5e7eb;padding:6px 8px;font-size:13px;} .text-end{text-align:right;} .text-center{text-align:center;} .text-success{color:#16a34a;} .text-danger{color:#dc2626;} .bg-light{background-color:#f9fafb;}</style>';
        html += '</head><body>' + pane.html() + '</body></html>';
        printWindow.document.open();
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    };

    $('#reportSearch').on('keyup', function () {
        var term = $(this).val().toLowerCase();
        $('.tab-content .tab-pane.active .report-table tbody tr').each(function () {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(term) !== -1);
        });
    });

    var finLabels = $financial_labels_json;
    var finSales = $financial_sales_json;
    var finReceived = $financial_received_json;
    var finExpenses = $financial_expenses_json;

    if (finLabels.length > 0 && $('#financial_bar_chart').length) {
        var financialOptions = {
            chart: {
                height: 320,
                type: 'bar',
                stacked: false
            },
            series: [{
                name: 'Total Deal',
                data: finSales
            }, {
                name: 'Collections',
                data: finReceived
            }, {
                name: 'Expenses',
                data: finExpenses
            }],
            xaxis: {
                categories: finLabels
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top'
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '₹ ' + val.toFixed(2);
                    }
                }
            }
        };
        var financialChart = new ApexCharts(document.querySelector('#financial_bar_chart'), financialOptions);
        financialChart.render();
    }

    var agingLabels = $aging_bucket_labels_json;
    var agingValues = $aging_bucket_values_json;

    if (agingLabels.length > 0 && $('#aging_bar_chart').length) {
        var agingOptions = {
            chart: {
                height: 320,
                type: 'bar'
            },
            series: [{
                name: 'Outstanding',
                data: agingValues
            }],
            xaxis: {
                categories: agingLabels
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#ff9f43'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '₹ ' + val.toFixed(2);
                    }
                }
            }
        };
        var agingChart = new ApexCharts(document.querySelector('#aging_bar_chart'), agingOptions);
        agingChart.render();
    }

    var vendorLabels = $vendor_labels_json;
    var vendorValues = $vendor_values_json;

    if (vendorLabels.length > 0 && $('#vendor_spend_chart').length) {
        var vendorOptions = {
            chart: {
                height: 320,
                type: 'bar'
            },
            series: [{
                name: 'Total Spent',
                data: vendorValues
            }],
            xaxis: {
                categories: vendorLabels
            },
            plotOptions: {
                bar: {
                    horizontal: true
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#17a2b8'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '₹ ' + val.toFixed(2);
                    }
                }
            }
        };
        var vendorChart = new ApexCharts(document.querySelector('#vendor_spend_chart'), vendorOptions);
        vendorChart.render();
    }

    var cashDates = $cashflow_dates_json;
    var cashIn = $cashflow_inflow_json;
    var cashOut = $cashflow_outflow_json;
    var cashNet = $cashflow_net_json;

    if (cashDates.length > 0 && $('#cashflow_line_chart').length) {
        var cashOptions = {
            chart: {
                height: 320,
                type: 'area'
            },
            series: [{
                name: 'Inflow',
                data: cashIn
            }, {
                name: 'Outflow',
                data: cashOut
            }, {
                name: 'Net',
                data: cashNet
            }],
            xaxis: {
                categories: cashDates
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '₹ ' + val.toFixed(2);
                    }
                }
            },
            colors: ['#16a34a', '#dc2626', '#0ea5e9'],
            legend: {
                position: 'top'
            }
        };
        var cashChart = new ApexCharts(document.querySelector('#cashflow_line_chart'), cashOptions);
        cashChart.render();
    }
});
</script>
EOT;

include 'includes/footer.php';
?>
