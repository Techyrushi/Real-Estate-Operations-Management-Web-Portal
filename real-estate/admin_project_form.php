<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_projects')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$id = $_GET['id'] ?? null;
$project = null;
$msg = "";
$error = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        $error = "Project not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $address = $_POST['address'] ?? '';
    $location_details = $_POST['location_details'] ?? '';
    $rera_reg = $_POST['rera_reg'] ?? '';
    $ready_reckoner_rate_res = $_POST['ready_reckoner_rate_res'] ?: 0;
    $ready_reckoner_rate_com = $_POST['ready_reckoner_rate_com'] ?: 0;
    $carpet_area = $_POST['carpet_area'] ?: 0;
    $sellable_area = $_POST['sellable_area'] ?: 0;
    $num_units = $_POST['num_units'] ?: 0;
    $status = $_POST['status'] ?? 'Planning';

    // Handle Image Upload
    $image_path = $project['image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/projects/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    if (!$error) {
        try {
            if ($id) {
                // Update
                $sql = "UPDATE projects SET name=?, type=?, address=?, location_details=?, rera_reg=?, ready_reckoner_rate_res=?, ready_reckoner_rate_com=?, carpet_area=?, sellable_area=?, num_units=?, status=?, image=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $type, $address, $location_details, $rera_reg, $ready_reckoner_rate_res, $ready_reckoner_rate_com, $carpet_area, $sellable_area, $num_units, $status, $image_path, $id])) {
                    $msg = "Project updated successfully.";
                    // Refresh data
                    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
                    $stmt->execute([$id]);
                    $project = $stmt->fetch();
                } else {
                    $error = "Failed to update project.";
                }
            } else {
                // Insert
                $sql = "INSERT INTO projects (name, type, address, location_details, rera_reg, ready_reckoner_rate_res, ready_reckoner_rate_com, carpet_area, sellable_area, num_units, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $type, $address, $location_details, $rera_reg, $ready_reckoner_rate_res, $ready_reckoner_rate_com, $carpet_area, $sellable_area, $num_units, $status, $image_path])) {
                    $msg = "Project created successfully.";
                    $id = $pdo->lastInsertId();
                    // Fetch new project data
                    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
                    $stmt->execute([$id]);
                    $project = $stmt->fetch();
                } else {
                    $error = "Failed to create project.";
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
                <h4 class="page-title"><?php echo $id ? 'Edit Project' : 'Add New Project'; ?></h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_projects.php">Projects</a></li>
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
                    <div class="box-header with-border">
                        <h4 class="box-title">Project Details</h4>
                    </div>
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
                                            window.location.href = 'admin_projects.php';
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

                        <form method="post" enctype="multipart/form-data" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($project['name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Project Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="Residential" <?php echo ($project['type'] ?? '') == 'Residential' ? 'selected' : ''; ?>>Residential</option>
                                            <option value="Commercial" <?php echo ($project['type'] ?? '') == 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
                                            <option value="Mixed" <?php echo ($project['type'] ?? '') == 'Mixed' ? 'selected' : ''; ?>>Mixed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">RERA Registration No.</label>
                                        <input type="text" class="form-control" name="rera_reg" value="<?php echo htmlspecialchars($project['rera_reg'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Project Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Planning" <?php echo ($project['status'] ?? '') == 'Planning' ? 'selected' : ''; ?>>Planning</option>
                                            <option value="Ongoing" <?php echo ($project['status'] ?? '') == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                            <option value="Completed" <?php echo ($project['status'] ?? '') == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Site Address</label>
                                        <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($project['address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Location Details / Landmarks</label>
                                        <textarea class="form-control" name="location_details" rows="3"><?php echo htmlspecialchars($project['location_details'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-12"><h5 class="mt-4 mb-3 text-primary">Technical Details</h5></div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Govt. Ready Reckoner Rate (Residential) ₹</label>
                                        <input type="number" step="0.01" class="form-control" name="ready_reckoner_rate_res" value="<?php echo htmlspecialchars($project['ready_reckoner_rate_res'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Govt. Ready Reckoner Rate (Commercial) ₹</label>
                                        <input type="number" step="0.01" class="form-control" name="ready_reckoner_rate_com" value="<?php echo htmlspecialchars($project['ready_reckoner_rate_com'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Total Number of Units</label>
                                        <input type="number" class="form-control" name="num_units" value="<?php echo htmlspecialchars($project['num_units'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Total Carpet Area (sq.ft)</label>
                                        <input type="number" step="0.01" class="form-control" name="carpet_area" value="<?php echo htmlspecialchars($project['carpet_area'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Total Sellable Area (sq.ft)</label>
                                        <input type="number" step="0.01" class="form-control" name="sellable_area" value="<?php echo htmlspecialchars($project['sellable_area'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Project Image</label>
                                        <input type="file" class="form-control" name="image">
                                        <?php if (!empty($project['image'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo $project['image']; ?>" width="100" class="rounded">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <a href="admin_projects.php" class="btn btn-warning me-1"><i class="ti-trash"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Project</button>
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