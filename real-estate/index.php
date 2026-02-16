<?php
include 'includes/header.php';
include 'includes/sidebar.php';

$dashboard_error = '';

$stats = [
    'total_projects' => 0,
    'total_units' => 0,
    'sold_units' => 0,
    'available_units' => 0,
    'total_income' => 0,
    'total_expenses' => 0,
    'receivables' => 0,
];
$project_types = [];
$partners = [];
$months = [];
$sales_data = [];
$collections_data = [];
$expenses_data = [];
$recent_bookings = [];

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
    $stats['total_projects'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM units");
    $stats['total_units'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM units WHERE status = 'Sold'");
    $stats['sold_units'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM units WHERE status = 'Available'");
    $stats['available_units'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT SUM(amount) FROM payments");
    $stats['total_income'] = (float) ($stmt->fetchColumn() ?: 0);

    $stmt = $pdo->query("SELECT SUM(amount) FROM expenses");
    $stats['total_expenses'] = (float) ($stmt->fetchColumn() ?: 0);

    $stmt = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status != 'Cancelled'");
    $total_booked_value = (float) ($stmt->fetchColumn() ?: 0);
    $stats['receivables'] = $total_booked_value - $stats['total_income'];

    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM projects GROUP BY type");
    $project_types = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $partners = $pdo->query("SELECT * FROM partners")->fetchAll();
    foreach ($partners as &$p) {
        $credits = $pdo->prepare("SELECT SUM(amount) FROM partner_ledger WHERE partner_id = ? AND type = 'Credit'");
        $credits->execute([$p['id']]);
        $total_credit = (float) ($credits->fetchColumn() ?: 0);

        $debits = $pdo->prepare("SELECT SUM(amount) FROM partner_ledger WHERE partner_id = ? AND type = 'Debit'");
        $debits->execute([$p['id']]);
        $total_debit = (float) ($debits->fetchColumn() ?: 0);

        $p['total_capital'] = (float) $p['opening_capital'] + $total_credit - $total_debit;
    }
    unset($p);
    usort($partners, function ($a, $b) {
        return $b['total_capital'] <=> $a['total_capital'];
    });

    for ($i = 5; $i >= 0; $i--) {
        $month_start = date('Y-m-01', strtotime("-$i months"));
        $month_end = date('Y-m-t', strtotime("-$i months"));
        $month_label = date('M Y', strtotime("-$i months"));
        $months[] = $month_label;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE booking_date BETWEEN :start AND :end");
        $stmt->execute(['start' => $month_start, 'end' => $month_end]);
        $sales_data[] = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE payment_date BETWEEN :start AND :end");
        $stmt->execute(['start' => $month_start, 'end' => $month_end]);
        $collections_data[] = (float) ($stmt->fetchColumn() ?: 0);

        $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE expense_date BETWEEN :start AND :end");
        $stmt->execute(['start' => $month_start, 'end' => $month_end]);
        $expenses_data[] = (float) ($stmt->fetchColumn() ?: 0);
    }

    $stmt = $pdo->query("
        SELECT b.*, c.name as customer_name, u.flat_no, p.name as project_name, u.property_type as unit_type
        FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        JOIN units u ON b.unit_id = u.id
        JOIN projects p ON u.project_id = p.id
        ORDER BY b.booking_date DESC
        LIMIT 10
    ");
    $recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dashboard_error = $e->getMessage();
}

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="container-full">
		<!-- Main content -->
		<section class="content">
            <?php if ($dashboard_error): ?>
                <div class="alert alert-danger">
                    Dashboard data error: <?php echo htmlspecialchars($dashboard_error); ?>
                </div>
            <?php endif; ?>
			<div class="row">
                <!-- Stats Cards -->
				<div class="col-xl-3 col-md-6 col-12">
                    <a href="#" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-700 mt-0"><?php echo $stats['total_projects']; ?></h3>
                                    <h4 class="text-fade mt-10 mb-0">Total Projects</h4>
                                </div>
                                <div class="p-10 bg-primary-light rounded-circle">
                                    <i class="text-primary" data-feather="home"></i>
                                </div>
                            </div>
                        </div>
                    </a>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
                    <a href="propertylist.php" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-700 mt-0"><?php echo $stats['sold_units']; ?> / <?php echo $stats['total_units']; ?></h3>
                                    <h4 class="text-fade mt-10 mb-0">Sold / Total Units</h4>
                                    <p class="mb-0 text-muted fs-12 mt-5">Available: <?php echo $stats['available_units']; ?></p>
                                </div>
                                <div class="p-10 bg-info-light rounded-circle">
                                    <i class="text-info" data-feather="check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </a>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
                    <a href="#" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-700 mt-0 text-success">₹<?php echo number_format($stats['total_income']); ?></h3>
                                    <h4 class="text-fade mt-10 mb-0">Total Collections</h4>
                                </div>
                                <div class="p-10 bg-success-light rounded-circle">
                                    <i class="text-success" data-feather="dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </a>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
                    <a href="#" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-700 mt-0 text-danger">₹<?php echo number_format($stats['total_expenses']); ?></h3>
                                    <h4 class="text-fade mt-10 mb-0">Total Expenses</h4>
                                    <p class="mb-0 text-muted fs-12 mt-5">Net: ₹<?php echo number_format($stats['total_income'] - $stats['total_expenses']); ?></p>
                                </div>
                                <div class="p-10 bg-danger-light rounded-circle">
                                    <i class="text-danger" data-feather="trending-down"></i>
                                </div>
                            </div>
                        </div>
                    </a>
				</div>

                <!-- Charts -->
				<div class="col-12 col-xl-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Project Types</h4>
						</div>
						<div class="box-body">
							<div id="project_type_pie" style="height: 285px;"></div>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Monthly Sales (Units)</h4>
						</div>
						<div class="box-body">
							<div id="monthly_sales_chart"></div>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Financial Trend (Income vs Expense)</h4>
						</div>
						<div class="box-body">
							<div id="financial_trend_chart"></div>
						</div>
					</div>
				</div>

                <!-- Detailed Summaries -->
				<div class="col-xl-4 col-12">
                    <!-- Partner Capital -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Partner Capital Summary</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Partner</th>
                                            <th>Capital</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($partners as $partner): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($partner['name']); ?></td>
                                            <td>₹<?php echo number_format($partner['total_capital']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($partners)): ?>
                                        <tr><td colspan="2" class="text-center">No partners found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Flow Summary -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Cash Flow Summary</h4>
                        </div>
                        <div class="box-body">
                             <div class="d-flex justify-content-between mb-10">
                                <span>Total Inflow:</span>
                                <span class="text-success">₹<?php echo number_format($stats['total_income']); ?></span>
                             </div>
                             <div class="d-flex justify-content-between mb-10">
                                <span>Total Outflow:</span>
                                <span class="text-danger">₹<?php echo number_format($stats['total_expenses']); ?></span>
                             </div>
                             <div class="d-flex justify-content-between border-top pt-10">
                                <strong>Net Cash Flow:</strong>
                                <strong class="<?php echo ($stats['total_income'] - $stats['total_expenses']) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    ₹<?php echo number_format($stats['total_income'] - $stats['total_expenses']); ?>
                                </strong>
                             </div>
                        </div>
                    </div>
				</div>

				<div class="col-xl-8 col-12">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Recent Bookings / Property Overview</h4>
						</div>
						<div class="box-body pt-10">
							<div class="table-responsive">
								<table class="table product-overview mb-0">
									<thead>
										<tr>
											<th>Customer</th>
											<th>Unit</th>
											<th>Project</th>
											<th>Type</th>
											<th>Date</th>
											<th>Status</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
                                        <?php foreach ($recent_bookings as $booking): ?>
										<tr>
											<td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
											<td><?php echo htmlspecialchars($booking['flat_no']); ?></td>   
											<td><?php echo htmlspecialchars($booking['project_name']); ?></td>
											<td><?php echo htmlspecialchars($booking['unit_type']); ?></td>
											<td><?php echo date('d-m-Y', strtotime($booking['booking_date'])); ?></td>
											<td>
                                                <?php
                                                $statusClass = 'label-warning';
                                                if ($booking['status'] == 'Confirmed') $statusClass = 'label-success';
                                                if ($booking['status'] == 'Cancelled') $statusClass = 'label-danger';
                                                ?>
                                                <span class="label <?php echo $statusClass; ?>"><?php echo htmlspecialchars($booking['status']); ?></span>
                                            </td>
											<td>
                                                <a href="javascript:void(0)" class="text-dark pe-10" data-bs-toggle="tooltip" title="Edit"><i class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip" title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($recent_bookings)): ?>
                                        <tr><td colspan="7" class="text-center">No recent bookings found</td></tr>
                                        <?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /.content -->
	</div>
</div>

<?php
$hide_dashboard_js = true;

// Prepare Data for JS
$months_json = json_encode($months);
$sales_json = json_encode($sales_data);
$collections_json = json_encode($collections_data);
$expenses_json = json_encode($expenses_data);
$project_types_data = [];
foreach ($project_types as $type => $count) {
    $project_types_data[] = ['label' => $type, 'data' => [[1, $count]]]; // Format for Flot Pie
}
$project_types_json = json_encode($project_types_data);

$extra_js = <<<EOT
<script>
$(function () {
    'use strict';

    // Project Types Pie Chart (Flot)
    var piedata = $project_types_json;
    if (piedata.length > 0 && $('#project_type_pie').length) {
        $.plot('#project_type_pie', piedata, {
          series: {
            pie: {
              show: true,
              radius: 1,
              innerRadius: 0.5,
              label: {
                show: true,
                radius: 2/3,
                formatter: function(label, series) {
                    return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
                },
                threshold: 0.1
              }
            }
          },
          grid: {
            hoverable: true,
            clickable: true
          }
        });
    }

    // Monthly Sales Chart (ApexCharts)
    if ($('#monthly_sales_chart').length) {
        var options = {
            chart: {
                height: 285,
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%'
                },
            },
            dataLabels: {
                enabled: false
            },
            colors: ["#40a2ed"],
            series: [{
                name: 'Sales (Units)',
                data: $sales_json
            }],
            xaxis: {
                categories: $months_json,
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Units"
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#monthly_sales_chart"), options);
        chart.render();
    }

    // Financial Trend Chart (ApexCharts)
    if ($('#financial_trend_chart').length) {
        var options = {
            chart: {
                height: 285,
                type: 'line',
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            colors: ["#28a745", "#dc3545"], // Green for Income, Red for Expense
            stroke: { curve: 'straight' },
            series: [{
                name: "Collections",
                data: $collections_json
            }, {
                name: "Expenses",
                data: $expenses_json
            }],
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: $months_json,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "₹ " + val
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#financial_trend_chart"), options);
        chart.render();
    }
});
</script>
EOT;

include 'includes/footer.php';
?>
