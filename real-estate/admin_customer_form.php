<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_customers')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$customer = null;
$booking = null;
$msg = "";
$error = "";

// Initialize variables
$selected_project_id = $_GET['project_id'] ?? null;

if ($id) {
    // Fetch Customer
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        $error = "Customer not found.";
    } else {
        // Fetch Booking
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();

        // If editing, use the booking's project_id via unit
        if ($booking && !$selected_project_id) {
            $unitStmt = $pdo->prepare("SELECT project_id FROM units WHERE id = ?");
            $unitStmt->execute([$booking['unit_id']]);
            $unitProject = $unitStmt->fetchColumn();
            if ($unitProject) {
                $selected_project_id = $unitProject;
            }
        }
    }
}

// Fetch Projects
$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();

// Fetch Units based on selected project
$units = [];
if ($selected_project_id) {
    // Show Available units OR the unit currently assigned to this customer (if editing)
    $unit_sql = "SELECT id, flat_no, area, price FROM units WHERE project_id = ? AND (status = 'Available'";
    $params = [$selected_project_id];
    
    if ($booking && $booking['unit_id']) {
        $unit_sql .= " OR id = ?";
        $params[] = $booking['unit_id'];
    }
    $unit_sql .= ") ORDER BY flat_no ASC";
    
    $stmt = $pdo->prepare($unit_sql);
    $stmt->execute($params);
    $units = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $pan = $_POST['pan'] ?? '';
    
    $project_id = $_POST['project_id'] ?? '';
    $unit_id = $_POST['unit_id'] ?: null;
    $rate_per_sqft = $_POST['rate_per_sqft'] ?: 0;
    $total_deal_amount = $_POST['total_deal_amount'] ?: 0;
    $agreement_value = $_POST['agreement_value'] ?: 0;
    $booking_date = $_POST['booking_date'] ?? '';
    $status = $_POST['status'] ?? ''; // Booked, Agreement Done, etc. -> map to bookings.status (Confirmed, Pending, Cancelled)
    
    // Mapping status for bookings table
    $booking_status = 'Pending';
    if ($status == 'Booked' || $status == 'Agreement Done' || $status == 'Completed') {
        $booking_status = 'Confirmed';
    } elseif ($status == 'Cancelled') {
        $booking_status = 'Cancelled';
    }

    if (!$error) {
        try {
            $pdo->beginTransaction();

            if ($id) {
                // Update Customer
                $sql = "UPDATE customers SET name=?, phone=?, email=?, address=?, pan=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $mobile, $email, $address, $pan, $id]);
                
                // Handle Booking
                if ($unit_id) {
                    $old_unit_id = $booking['unit_id'] ?? null;
                    
                    // If unit changed
                    if ($old_unit_id && $old_unit_id != $unit_id) {
                        // Make old unit Available
                        $pdo->prepare("UPDATE units SET status = 'Available' WHERE id = ?")->execute([$old_unit_id]);
                    }
                    
                    // Make new unit Sold
                    $pdo->prepare("UPDATE units SET status = 'Sold' WHERE id = ?")->execute([$unit_id]);

                    if ($booking) {
                        // Update existing booking
                        $sql = "UPDATE bookings SET unit_id=?, total_price=?, agreement_value=?, rate_per_sqft=?, booking_date=?, status=? WHERE id=?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$unit_id, $total_deal_amount, $agreement_value, $rate_per_sqft, $booking_date, $booking_status, $booking['id']]);
                    } else {
                        // Create new booking for existing customer
                        $sql = "INSERT INTO bookings (customer_id, unit_id, total_price, agreement_value, rate_per_sqft, booking_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$id, $unit_id, $total_deal_amount, $agreement_value, $rate_per_sqft, $booking_date, $booking_status]);
                    }
                }

                $msg = "Customer updated successfully.";
            } else {
                // New Customer
                $sql = "INSERT INTO customers (name, phone, email, address, pan) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $mobile, $email, $address, $pan]);
                $id = $pdo->lastInsertId();

                // Create Booking
                if ($unit_id) {
                    $pdo->prepare("UPDATE units SET status = 'Sold' WHERE id = ?")->execute([$unit_id]);
                    
                    $sql = "INSERT INTO bookings (customer_id, unit_id, total_price, agreement_value, rate_per_sqft, booking_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$id, $unit_id, $total_deal_amount, $agreement_value, $rate_per_sqft, $booking_date, $booking_status]);
                }
                
                $msg = "Customer added successfully.";
            }

            $pdo->commit();

            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$id]);
            $customer = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$id]);
            $booking = $stmt->fetch();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to save customer: " . $e->getMessage();
        }
    }
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title"><?php echo $id ? 'Edit Customer' : 'Add New Customer'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_customers.php">Customers</a></li>
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
                                            window.location.href = 'admin_customers.php';
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

                        <form method="post" id="customerForm" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <h4 class="box-title text-info mb-0"><i class="ti-user me-15"></i> Personal Details</h4>
                            <hr class="my-15">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($customer['phone'] ?? $customer['mobile'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Email ID</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">PAN Number</label>
                                        <input type="text" class="form-control" name="pan" value="<?php echo htmlspecialchars($customer['pan'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title text-info mb-0 mt-20"><i class="ti-home me-15"></i> Booking Details</h4>
                            <hr class="my-15">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Project <span class="text-danger">*</span></label>
                                        <select class="form-select" name="project_id" onchange="window.location.href='?<?php echo $id ? 'id='.$id.'&' : ''; ?>project_id='+this.value" required>
                                            <option value="">Select Project</option>
                                            <?php foreach ($projects as $p): ?>
                                                <option value="<?php echo $p['id']; ?>" <?php echo ($selected_project_id == $p['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($p['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Select Unit</label>
                                        <select class="form-select" name="unit_id" id="unit_select">
                                            <option value="">Select Unit</option>
                                            <?php foreach ($units as $u): ?>
                                                <option value="<?php echo $u['id']; ?>" 
                                                    data-area="<?php echo $u['area']; ?>" 
                                                    data-price="<?php echo $u['price']; ?>"
                                                    <?php echo ($booking['unit_id'] ?? '') == $u['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($u['flat_no'] . ' (' . $u['area'] . ' sqft)'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Rate per sq.ft</label>
                                        <input type="number" step="0.01" class="form-control" name="rate_per_sqft" id="rate_per_sqft" value="<?php echo $booking['rate_per_sqft'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Total Deal Amount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="total_deal_amount" id="total_deal_amount" value="<?php echo $booking['total_price'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Agreement Value (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="agreement_value" value="<?php echo $booking['agreement_value'] ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Booking Date</label>
                                        <input type="date" class="form-control" name="booking_date" value="<?php echo $booking['booking_date'] ?? date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Booked" <?php echo ($booking['status'] ?? '') == 'Confirmed' ? 'selected' : ''; ?>>Booked</option>
                                            <option value="Agreement Done" <?php echo ($booking['status'] ?? '') == 'Confirmed' ? 'selected' : ''; ?>>Agreement Done</option>
                                            <option value="Completed" <?php echo ($booking['status'] ?? '') == 'Confirmed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Cancelled" <?php echo ($booking['status'] ?? '') == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Customer</button>
                                <a href="admin_customers.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<script>
    // Simple script to auto-fill details when unit is selected
    document.getElementById('unit_select').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var area = selectedOption.getAttribute('data-area');
        // var price = selectedOption.getAttribute('data-price');
        
        // You can add logic to populate fields if needed
    });

    // Example calculation logic if you had an area field input
    // document.getElementById('rate_per_sqft').addEventListener('input', calculateTotal);

    // function calculateTotal() {
    //     var area = parseFloat(document.getElementById('sellable_area').value) || 0;
    //     var rate = parseFloat(document.getElementById('rate_per_sqft').value) || 0;
    //     if (area && rate) {
    //         document.getElementById('total_deal_amount').value = (area * rate).toFixed(2);
    //     }
    // }
</script>

<?php include 'includes/footer.php'; ?>
