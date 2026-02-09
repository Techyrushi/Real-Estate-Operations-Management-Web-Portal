<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_projects')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch all projects
$stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
$projects = $stmt->fetchAll();
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
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Projects</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_project_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Project</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="projects_table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Project Name</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Units</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td>#<?php echo $project['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if(!empty($project['image'])): ?>
                                                <img src="<?php echo $project['image']; ?>" class="avatar avatar-sm rounded-circle me-2" alt="">
                                                <?php else: ?>
                                                <div class="avatar avatar-sm bg-primary-light rounded-circle me-2"><i class="ti-home"></i></div>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($project['name']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($project['rera_reg']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($project['type']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($project['address'], 0, 30)) . '...'; ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $project['num_units']; ?> Units</span>
                                        </td>
                                        <td>
                                            <?php 
                                            $status = $project['status'] ?? 'Planning';
                                            $badgeClass = 'badge-warning';
                                            if($status == 'Completed') $badgeClass = 'badge-success';
                                            if($status == 'Ongoing') $badgeClass = 'badge-primary';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                        </td>
                                        <td>
                                            <a href="admin_project_form.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                            <a href="admin_project_units.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Manage Units"><i class="ti-layout-grid2"></i></a>
                                            <a href="admin_project_view.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="View Details"><i class="ti-eye"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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

<?php include 'includes/footer.php'; ?>