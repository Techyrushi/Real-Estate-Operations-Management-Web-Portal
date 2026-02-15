<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_vendors')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$vendor = null;
$msg = "";
$error = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
    $stmt->execute([$id]);
    $vendor = $stmt->fetch();
    if (!$vendor) {
        $error = "Vendor not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $gst_number = $_POST['gst_number'] ?? '';
    $contact_details = $_POST['contact_details'] ?? '';
    $bank_details = $_POST['bank_details'] ?? '';
    $material_category = $_POST['material_category'] ?? '';
    $status = $_POST['status'] ?? 'Active';

    if (!$error) {
        try {
            if ($id) {
                $sql = "UPDATE vendors SET name=?, gst_number=?, contact_details=?, bank_details=?, material_category=?, status=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $gst_number, $contact_details, $bank_details, $material_category, $status, $id])) {  
                    $msg = "Vendor updated successfully.";
                    $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
                    $stmt->execute([$id]);
                    $vendor = $stmt->fetch();
                } else {
                    $error = "Failed to update vendor.";
                }
            } else {
                $sql = "INSERT INTO vendors (name, gst_number, contact_details, bank_details, material_category, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $gst_number, $contact_details, $bank_details, $material_category, $status])) {   
                    $msg = "Vendor created successfully.";
                    $id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
                    $stmt->execute([$id]);
                    $vendor = $stmt->fetch();
                } else {
                    $error = "Failed to create vendor.";
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
                <h4 class="page-title"><?php echo $id ? 'Edit Vendor' : 'Add New Vendor'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_vendors.php">Vendors</a></li>
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
                                            window.location.href = 'admin_vendors.php';
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

                        <form method="post" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($vendor['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">GST Number</label>
                                        <input type="text" class="form-control" name="gst_number" value="<?php echo htmlspecialchars($vendor['gst_number'] ?? ''); ?>"> 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Contact Details (Address, Phone, Email)</label>
                                        <textarea class="form-control" name="contact_details" rows="3"><?php echo htmlspecialchars($vendor['contact_details'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Bank Details (Bank, A/c No, IFSC)</label>
                                        <textarea class="form-control" name="bank_details" rows="3"><?php echo htmlspecialchars($vendor['bank_details'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Material / Service Provided</label>
                                        <textarea class="form-control" name="material_category" rows="3" placeholder="e.g. Cement, Steel, Electrical Work"><?php echo htmlspecialchars($vendor['material_category'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Active" <?php echo ($vendor['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($vendor['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save</button>
                                <a href="admin_vendors.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
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
