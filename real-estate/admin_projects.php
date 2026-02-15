<?php
include 'includes/header.php';
include 'includes/sidebar.php';

if (!hasRole('Admin') && !hasPermission('manage_projects')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$msg = '';
$error = '';

// Current filter query string reused for export links
$export_query = http_build_query($_GET);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['follow_project_id']) && isset($_SESSION['user_id'])) {
    $project_id = (int)$_POST['follow_project_id'];
    $is_now_followed = false;
    if ($project_id > 0) {
        $user_id = (int)$_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT id FROM user_follows WHERE user_id = ? AND entity_type = 'project' AND entity_id = ?");
        $stmt->execute([$user_id, $project_id]);
        if ($stmt->fetchColumn()) {
            $del = $pdo->prepare("DELETE FROM user_follows WHERE user_id = ? AND entity_type = 'project' AND entity_id = ?");
            $del->execute([$user_id, $project_id]);
            $is_now_followed = false;
        } else {
            $ins = $pdo->prepare("INSERT IGNORE INTO user_follows (user_id, entity_type, entity_id) VALUES (?, 'project', ?)");
            $ins->execute([$user_id, $project_id]);
            $is_now_followed = true;
        }
    }

    $msg = $is_now_followed ? 'Project followed successfully.' : 'Project unfollowed successfully.';
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int) $_GET['id'];
    if ($delete_id > 0) {
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$delete_id]);

            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            $msg = 'Project deleted successfully.';
        } catch (PDOException $e) {
            try {
                $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            } catch (Exception $ignored) {
            }
            $error = 'Unable to delete project.';
        }
    }
}

// Filters
$filter_type = $_GET['type'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_city = $_GET['city'] ?? '';
$filter_search = trim($_GET['search'] ?? '');
$filter_followed = $_GET['followed'] ?? '';

// Load filter options
$cities = $pdo->query("SELECT DISTINCT city FROM projects WHERE city IS NOT NULL AND city <> '' ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);

// Build base query (reused for export and listing)
$sql = "SELECT * FROM projects WHERE 1=1";
$params = [];

if ($filter_type !== '') {
    $sql .= " AND type = ?";
    $params[] = $filter_type;
}

if ($filter_status !== '') {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}

if ($filter_city !== '') {
    $sql .= " AND city = ?";
    $params[] = $filter_city;
}

if ($filter_search !== '') {
    $sql .= " AND (name LIKE ? OR rera_reg LIKE ? OR address LIKE ?)";
    $like = '%' . $filter_search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($filter_followed === '1' && isset($_SESSION['user_id'])) {
    $sql .= " AND id IN (SELECT entity_id FROM user_follows WHERE user_id = ? AND entity_type = 'project')";
    $params[] = (int) $_SESSION['user_id'];
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$total_projects = count($projects);
$ongoing_projects = 0;
$completed_projects = 0;
foreach ($projects as $p) {
    $status_val = $p['status'] ?? 'Planning';
    if ($status_val === 'Ongoing') {
        $ongoing_projects++;
    } elseif ($status_val === 'Completed') {
        $completed_projects++;
    }
}

$followed_projects = [];
if (!empty($projects) && isset($_SESSION['user_id'])) {
    $project_ids = array_column($projects, 'id');
    $placeholders = implode(',', array_fill(0, count($project_ids), '?'));
    $sql_follow = "SELECT entity_id FROM user_follows WHERE user_id = ? AND entity_type = 'project'";
    $params_follow = [(int) $_SESSION['user_id']];
    if ($placeholders) {
        $sql_follow .= " AND entity_id IN ($placeholders)";
        $params_follow = array_merge($params_follow, $project_ids);
    }
    $stmt_follow = $pdo->prepare($sql_follow);
    $stmt_follow->execute($params_follow);
    $followed_projects = $stmt_follow->fetchAll(PDO::FETCH_COLUMN);
}

$flash_msg = $msg !== '' ? $msg : ($_GET['msg'] ?? '');
$flash_error = $error !== '' ? $error : ($_GET['error'] ?? '');
?>

<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h4 class="page-title">Project Master</h4>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Projects</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="ms-auto">
                    <a href="admin_projects_export.php<?php echo $export_query ? '?' . htmlspecialchars($export_query) : ''; ?>" class="btn btn-success btn-sm me-2"><i
                            class="ti-download"></i> Export Excel</a>
                    <a href="admin_project_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New
                        Project</a>
                </div>
            </div>
        </div>

        <section class="content">
            <?php if ($flash_msg): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: '<?php echo $flash_msg; ?>'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'admin_projects.php';
                            }
                        });
                    });
                </script>
            <?php endif; ?>
            <?php if ($flash_error): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '<?php echo $flash_error; ?>'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'admin_projects.php';
                            }
                        });
                    });
                </script>
            <?php endif; ?>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-primary-light">
                                <div class="box-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-10">
                                            <span class="badge bg-primary"><i
                                                    class="mdi mdi-office-building fs-20"></i></span>
                                        </div>
                                        <div>
                                            <h4 class="mb-0"><?php echo $total_projects; ?></h4>
                                            <span class="text-muted">Total Projects</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-info-light">
                                <div class="box-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-10">
                                            <span class="badge bg-info"><i
                                                    class="mdi mdi-progress-clock fs-20"></i></span>
                                        </div>
                                        <div>
                                            <h4 class="mb-0"><?php echo $ongoing_projects; ?></h4>
                                            <span class="text-muted">Ongoing</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-success-light">
                                <div class="box-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-10">
                                            <span class="badge bg-success"><i
                                                    class="mdi mdi-check-circle fs-20"></i></span>
                                        </div>
                                        <div>
                                            <h4 class="mb-0"><?php echo $completed_projects; ?></h4>
                                            <span class="text-muted">Completed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-warning-light">
                                <div class="box-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-10">
                                            <span class="badge bg-warning"><i
                                                    class="mdi mdi-chart-line fs-20"></i></span>
                                        </div>
                                        <div>
                                            <h4 class="mb-0">
                                                <?php echo $total_projects > 0 ? round(($ongoing_projects / max($total_projects, 1)) * 100) : 0; ?>%
                                            </h4>
                                            <span class="text-muted">Ongoing %</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body">
                            <form method="GET" action="admin_projects.php" class="mb-3">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Project Type</label>
                                            <select name="type" class="form-select">
                                                <option value="">All Types</option>
                                                <option value="Residential" <?php echo $filter_type === 'Residential' ? 'selected' : ''; ?>>Residential</option>
                                                <option value="Commercial" <?php echo $filter_type === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
                                                <option value="Mixed" <?php echo $filter_type === 'Mixed' ? 'selected' : ''; ?>>Mixed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="">All Status</option>
                                                <option value="Planning" <?php echo $filter_status === 'Planning' ? 'selected' : ''; ?>>Planning</option>
                                                <option value="Ongoing" <?php echo $filter_status === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                                <option value="Completed" <?php echo $filter_status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">City</label>
                                            <select name="city" class="form-select">
                                                <option value="">All Cities</option>
                                                <?php foreach ($cities as $city): ?>
                                                    <option value="<?php echo htmlspecialchars($city); ?>" <?php echo $filter_city === $city ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($city); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Search</label>
                                            <div class="d-flex">
                                                <input type="text" name="search" class="form-control me-2"
                                                    placeholder="Name, RERA, address"
                                                    value="<?php echo htmlspecialchars($filter_search); ?>">
                                                <button type="submit" class="btn btn-primary"><i
                                                        class="ti-search"></i></button>
                                                <a href="admin_projects.php" class="btn btn-secondary ms-2"><i
                                                        class="ti-reload"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Followed</label>
                                            <select name="followed" class="form-select">
                                                <option value="">All Projects</option>
                                                <option value="1" <?php echo $filter_followed === '1' ? 'selected' : ''; ?>>Followed Only</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <?php if (!empty($projects)): ?>
                                    <?php foreach ($projects as $project): ?>
                                        <?php
                                        $img_src = !empty($project['image']) ? $project['image'] : '../images/avatar/1.jpg';
                                        $status = $project['status'] ?? 'Planning';
                                        $badgeClass = 'badge-warning';
                                        if ($status == 'Completed')
                                            $badgeClass = 'badge-success';
                                        if ($status == 'Ongoing')
                                            $badgeClass = 'badge-primary';
                                        $address_short = !empty($project['address']) ? substr($project['address'], 0, 80) . '...' : '';
                                        ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                                            <div class="box">
                                                <div class="box-body p-0">
                                                    <div class="position-relative">
                                                        <img class="img-fluid" src="<?php echo $img_src; ?>" alt="project"
                                                            style="width: 100%; height: 200px; object-fit: cover;">
                                                        <span
                                                            class="badge <?php echo $badgeClass; ?> position-absolute top-0 end-0 m-10"><?php echo htmlspecialchars($status); ?></span>
                                                    </div>
                                                    <div class="p-20">
                                                        <h4 class="mt-0 mb-5">
                                                            <a href="admin_project_view.php?id=<?php echo $project['id']; ?>"
                                                                class="text-primary">
                                                                <?php echo htmlspecialchars($project['name']); ?>
                                                            </a>
                                                        </h4>
                                                        <p class="text-muted fs-12 mb-5">
                                                            <?php if (!empty($project['rera_reg'])): ?>
                                                                <span class="badge badge-info me-5">RERA:
                                                                    <?php echo htmlspecialchars($project['rera_reg']); ?></span>
                                                            <?php endif; ?>
                                                            <span
                                                                class="badge badge-secondary"><?php echo htmlspecialchars($project['type']); ?></span>
                                                        </p>
                                                        <p class="text-muted fs-12 mb-10">
                                                            <i
                                                                class="mdi mdi-map-marker me-5"></i><?php echo htmlspecialchars($address_short); ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center mt-10">
                                                            <span><i
                                                                    class="mdi mdi-view-grid me-5"></i><?php echo (int) ($project['num_units'] ?? 0); ?>
                                                                Units</span>
                                                            <span><i
                                                                    class="mdi mdi-vector-square me-5"></i><?php echo htmlspecialchars($project['carpet_area'] ?? 0); ?>
                                                                SqFt</span>
                                                        </div>
                                                        <div class="mt-15 d-flex justify-content-between">
                                                            <form method="POST" action="admin_projects.php" class="d-inline follow-project-form">
                                                                <input type="hidden" name="follow_project_id"
                                                                    value="<?php echo (int) $project['id']; ?>">
                                                                <?php
                                                                $is_followed = in_array($project['id'], $followed_projects);
                                                                $follow_class = $is_followed ? 'btn-success' : 'btn-outline-success';
                                                                $follow_title = $is_followed ? 'Unfollow' : 'Follow';
                                                                ?>
                                                                <button type="submit"
                                                                    class="btn btn-sm <?php echo $follow_class; ?>"
                                                                    title="<?php echo $follow_title; ?>">
                                                                    <i class="ti-heart"></i>
                                                                </button>
                                                            </form>
                                                            <div class="btn-group">
                                                                <a href="admin_project_form.php?id=<?php echo $project['id']; ?>"
                                                                    class="btn btn-sm btn-info" title="Edit"><i
                                                                        class="ti-pencil"></i></a>
                                                                <a href="admin_project_units.php?id=<?php echo $project['id']; ?>"
                                                                    class="btn btn-sm btn-warning" title="Units"><i
                                                                        class="ti-layout-grid2"></i></a>
                                                                <a href="admin_project_view.php?id=<?php echo $project['id']; ?>"
                                                                    class="btn btn-sm btn-primary" title="View"><i
                                                                        class="ti-eye"></i></a>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    title="Delete"
                                                                    onclick="confirmDeleteProject(<?php echo (int) $project['id']; ?>)"><i
                                                                        class="ti-trash"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="box">
                                            <div class="box-body text-center">
                                                <i class="ti-alert fs-50 text-warning"></i>
                                                <h3 class="mt-20">No projects found.</h3>
                                                <a href="admin_projects.php" class="btn btn-info mt-10">Clear Filters</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    function confirmDeleteProject(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the project and related records.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'admin_projects.php?action=delete&id=' + id;
            }
        });
    }

    // No extra JS needed for follow; form posts and SweetAlert uses flash message
</script>

<?php include 'includes/footer.php'; ?>
