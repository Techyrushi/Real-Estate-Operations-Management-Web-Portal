<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_vendors')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$item = null;
$msg = "";
$error = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        $error = "Material not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $unit_measure = $_POST['unit_measure'] ?? '';   
    $standard_rate = $_POST['standard_rate'] ?: 0;
    $status = $_POST['status'] ?? 'Active';

    if (!$error) {
        try {
            if ($id) {
                $sql = "UPDATE materials SET name=?, category=?, unit_measure=?, standard_rate=?, status=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $category, $unit_measure, $standard_rate, $status, $id])) {  
                    $msg = "Material updated successfully.";
                    $stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
                    $stmt->execute([$id]);
                    $item = $stmt->fetch();
                } else {
                    $error = "Failed to update material.";
                }
            } else {
                $sql = "INSERT INTO materials (name, category, unit_measure, standard_rate, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $category, $unit_measure, $standard_rate, $status])) {   
                    $msg = "Material created successfully.";
                    $id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
                    $stmt->execute([$id]);
                    $item = $stmt->fetch();
                } else {
                    $error = "Failed to create material.";
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
                <h4 class="page-title"><?php echo $id ? 'Edit Material' : 'Add New Material'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_materials.php">Materials</a></li>
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
                                            window.location.href = 'admin_materials.php';
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
                                        <label class="form-label">Material / Service Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($item['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category">
                                            <option value="Raw Material" <?php echo ($item['category'] ?? '') == 'Raw Material' ? 'selected' : ''; ?>>Raw Material</option>
                                            <option value="Finished Goods" <?php echo ($item['category'] ?? '') == 'Finished Goods' ? 'selected' : ''; ?>>Finished Goods</option>
                                            <option value="Service" <?php echo ($item['category'] ?? '') == 'Service' ? 'selected' : ''; ?>>Service</option>
                                            <option value="Labor" <?php echo ($item['category'] ?? '') == 'Labor' ? 'selected' : ''; ?>>Labor</option>
                                            <option value="Other" <?php echo ($item['category'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Unit of Measurement (UOM)</label>
                                        <input type="text" class="form-control" name="unit_measure" value="<?php echo htmlspecialchars($item['unit_measure'] ?? ''); ?>" placeholder="e.g. Kg, Ton, Bag, Sq.ft">    
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Standard Rate (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="standard_rate" value="<?php echo htmlspecialchars($item['standard_rate'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Active" <?php echo ($item['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="Inactive" <?php echo ($item['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save</button>
                                <a href="admin_materials.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
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
