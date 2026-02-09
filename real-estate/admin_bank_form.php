<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_banks')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$bank = null;
$msg = "";
$error = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM banks WHERE id = ?");
    $stmt->execute([$id]);
    $bank = $stmt->fetch();
    if (!$bank) {
        $error = "Bank not found.";
    }
}

// Fetch Projects for Dropdown
$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = $_POST['project_id'] ?: null;
    $bank_name = $_POST['bank_name'] ?? '';
    $branch = $_POST['branch'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $ifsc_code = $_POST['ifsc_code'] ?? '';
    $account_type = $_POST['account_type'] ?? '';
    $status = $_POST['status'] ?? 'Active';

    if (!$error) {
        try {
            if ($id) {
                // Update
                $sql = "UPDATE banks SET project_id=?, bank_name=?, branch=?, account_number=?, ifsc_code=?, account_type=?, status=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$project_id, $bank_name, $branch, $account_number, $ifsc_code, $account_type, $status, $id])) {
                    $msg = "Bank updated successfully.";
                    $stmt = $pdo->prepare("SELECT * FROM banks WHERE id = ?");
                    $stmt->execute([$id]);
                    $bank = $stmt->fetch();
                } else {
                    $error = "Failed to update bank.";
                }
            } else {
                // Insert
                $sql = "INSERT INTO banks (project_id, bank_name, branch, account_number, ifsc_code, account_type, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$project_id, $bank_name, $branch, $account_number, $ifsc_code, $account_type, $status])) {
                    $msg = "Bank created successfully.";
                    $id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("SELECT * FROM banks WHERE id = ?");
                    $stmt->execute([$id]);
                    $bank = $stmt->fetch();
                } else {
                    $error = "Failed to create bank.";
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
                <h4 class="page-title"><?php echo $id ? 'Edit Bank' : 'Add New Bank'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_banks.php">Banks</a></li>
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
                                            window.location.href = 'admin_banks.php';
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
                                        <label class="form-label">Linked Project</label>
                                        <select class="form-select" name="project_id">
                                            <option value="">General (No Specific Project)</option>
                                            <?php foreach ($projects as $proj): ?>
                                                <option value="<?php echo $proj['id']; ?>" <?php echo ($bank['project_id'] ?? '') == $proj['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($proj['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bank_name" value="<?php echo htmlspecialchars($bank['bank_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Branch Name</label>
                                        <input type="text" class="form-control" name="branch" value="<?php echo htmlspecialchars($bank['branch'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="account_number" value="<?php echo htmlspecialchars($bank['account_number'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ifsc_code" value="<?php echo htmlspecialchars($bank['ifsc_code'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Account Type</label>
                                        <select class="form-select" name="account_type">
                                            <option value="Construction" <?php echo ($bank['account_type'] ?? '') == 'Construction' ? 'selected' : ''; ?>>Construction</option>
                                            <option value="RERA Escrow" <?php echo ($bank['account_type'] ?? '') == 'RERA Escrow' ? 'selected' : ''; ?>>RERA Escrow</option>
                                            <option value="Collection" <?php echo ($bank['account_type'] ?? '') == 'Collection' ? 'selected' : ''; ?>>Collection</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Active" <?php echo ($bank['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($bank['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <a href="admin_banks.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Bank</button>
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