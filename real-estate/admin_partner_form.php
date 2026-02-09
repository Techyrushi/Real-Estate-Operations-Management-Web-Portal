<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_partners')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$partner = null;
$msg = "";
$error = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
    $stmt->execute([$id]);
    $partner = $stmt->fetch();
    if (!$partner) {
        $error = "Partner not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $percentage_share = $_POST['percentage_share'] ?: 0;
    $opening_capital = $_POST['opening_capital'] ?: 0;
    $status = $_POST['status'] ?? 'Active';

    if (!$error) {
        try {
            if ($id) {
                // Update
                $sql = "UPDATE partners SET name=?, email=?, mobile=?, percentage_share=?, opening_capital=?, status=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $email, $mobile, $percentage_share, $opening_capital, $status, $id])) {
                    $msg = "Partner updated successfully.";
                    // Refresh
                    $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
                    $stmt->execute([$id]);
                    $partner = $stmt->fetch();
                } else {
                    $error = "Failed to update partner.";
                }
            } else {
                // Insert
                $sql = "INSERT INTO partners (name, email, mobile, percentage_share, opening_capital, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $email, $mobile, $percentage_share, $opening_capital, $status])) {
                    $msg = "Partner created successfully.";
                    $id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
                    $stmt->execute([$id]);
                    $partner = $stmt->fetch();
                } else {
                    $error = "Failed to create partner.";
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
                <h4 class="page-title"><?php echo $id ? 'Edit Partner' : 'Add New Partner'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_partners.php">Partners</a></li>
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
                                            window.location.href = 'admin_partners.php';
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
                                        <label class="form-label">Partner Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($partner['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Active" <?php echo ($partner['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($partner['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email ID</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($partner['email'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($partner['mobile'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Percentage Share (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="percentage_share" value="<?php echo htmlspecialchars($partner['percentage_share'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Opening Capital (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="opening_capital" value="<?php echo htmlspecialchars($partner['opening_capital'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <a href="admin_partners.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Partner</button>
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