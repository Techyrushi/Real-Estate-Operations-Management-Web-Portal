<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_vendors')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$msg = '';
$error = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int) $_GET['id'];
    if ($delete_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM materials WHERE id = ?");
            if ($stmt->execute([$delete_id])) {
                $msg = 'Material deleted successfully.';
            } else {
                $error = 'Unable to delete material.';
            }
        } catch (PDOException $e) {
            $error = 'Unable to delete material.';
        }
    }
}

$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$category_filter = $_GET['category'] ?? '';

$sql = "SELECT * FROM materials WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR unit_measure LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
}

if ($category_filter !== '') {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}

if ($status_filter !== '') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll();

// Fetch distinct categories for dropdown filter
$category_options = $pdo->query("SELECT DISTINCT category FROM materials WHERE category IS NOT NULL AND category <> '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Build export query string (preserve current filters)
$export_params = $_GET;
unset($export_params['action'], $export_params['id']);
$export_params['export'] = 'excel';
$export_query = http_build_query($export_params);

// Excel export
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    if (!empty($materials)) {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="materials_master_' . date('Ymd_His') . '.xls"');
        echo "<table border='1'>";
        echo "<tr>
                <th>Name</th>
                <th>Category</th>
                <th>Unit of Measurement</th>
                <th>Standard Rate</th>
                <th>Status</th>
                <th>Created At</th>
              </tr>";
        foreach ($materials as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['category'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['unit_measure'] ?? '') . "</td>";
            echo "<td>" . number_format($row['standard_rate'] ?? 0, 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['status'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Data Not Found";
    }
    exit;
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Material / Service Master</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Materials</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_materials.php?<?php echo htmlspecialchars($export_query); ?>" class="btn btn-success btn-sm me-2"><i class="ti-download"></i> Export Excel</a>
                <a href="admin_material_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Material</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form method="GET" action="admin_materials.php" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or unit of measurement (UOM)" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($category_options as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter === $cat ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $status_filter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-primary me-2"><i class="ti-search"></i></button>
                                <a href="admin_materials.php" class="btn btn-secondary"><i class="ti-reload"></i></a>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="materials_table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Unit of Measurement (UOM)</th>
                                        <th>Standard Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($materials)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No materials found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($materials as $item): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($item['name'] ?? ''); ?></strong></td>
                                            <td><?php echo htmlspecialchars($item['category'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($item['unit_measure'] ?? ''); ?></td>
                                            <td>₹ <?php echo number_format($item['standard_rate'] ?? 0, 2); ?></td>
                                            <td>
                                                <?php 
                                                $status = $item['status'] ?? 'Active';
                                                $badgeClass = ($status == 'Active') ? 'badge-success' : 'badge-danger';
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                            </td>
                                            <td>
                                                <!-- <a href="admin_material_form.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary me-1" data-bs-toggle="tooltip" title="View"><i class="ti-eye"></i></a> -->
                                                <a href="admin_material_form.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete" onclick="confirmDeleteMaterial(<?php echo (int) ($item['id'] ?? 0); ?>)"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<script>
function confirmDeleteMaterial(id) {
    if (!id) return;
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the material record.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_materials.php?action=delete&id=' + id;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
