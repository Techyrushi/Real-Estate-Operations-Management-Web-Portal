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
if (!$id) {
    echo "<script>window.location.href='admin_projects.php';</script>";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Project Not Found</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

// Stats
$stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM units WHERE project_id = ? GROUP BY status");
$stmt->execute([$id]);
$unitStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$totalUnits = $project['num_units'];
$bookedUnits = $unitStats['Booked'] ?? 0;
$soldUnits = $unitStats['Sold'] ?? 0;
$availableUnits = $unitStats['Available'] ?? 0;

?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Project Details</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_projects.php">Projects</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($project['name']); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_project_form.php?id=<?php echo $project['id']; ?>" class="btn btn-info btn-sm me-2"><i class="ti-pencil"></i> Edit</a>
                <a href="admin_project_units.php?id=<?php echo $project['id']; ?>" class="btn btn-warning btn-sm"><i class="ti-layout-grid2"></i> Manage Units</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="box">
                    <div class="box-body box-profile">
                        <?php if(!empty($project['image'])): ?>
                            <img class="profile-user-img rounded-circle img-fluid mx-auto d-block" src="<?php echo $project['image']; ?>" alt="Project Image">
                        <?php else: ?>
                            <div class="profile-user-img rounded-circle img-fluid mx-auto d-block bg-primary-light text-center" style="line-height: 100px; font-size: 40px;"><i class="ti-home"></i></div>
                        <?php endif; ?>

                        <h3 class="profile-username text-center"><?php echo htmlspecialchars($project['name']); ?></h3>
                        <p class="text-muted text-center"><?php echo htmlspecialchars($project['type']); ?></p>

                        <div class="row">
                            <div class="col-12">
                                <div class="profile-user-info">
                                    <p>Email : <span class="text-gray ps-10">N/A</span> </p>
                                    <p>Phone :<span class="text-gray ps-10">N/A</span></p>
                                    <p>Address :<span class="text-gray ps-10"><?php echo htmlspecialchars($project['address']); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Key Stats</h4>
                    </div>
                    <div class="box-body">
                        <div class="d-flex align-items-center mb-30">
                            <div class="me-15 bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                                <span class="icon-Library fs-24"><span class="path1"></span><span class="path2"></span></span>
                            </div>
                            <div class="d-flex flex-column fw-500">
                                <a href="#" class="text-dark hover-primary mb-1 fs-16">RERA Reg</a>
                                <span class="text-fade"><?php echo htmlspecialchars($project['rera_reg']); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-30">
                            <div class="me-15 bg-danger-light h-50 w-50 l-h-60 rounded text-center">
                                <span class="icon-Write fs-24"><span class="path1"></span><span class="path2"></span></span>
                            </div>
                            <div class="d-flex flex-column fw-500">
                                <a href="#" class="text-dark hover-primary mb-1 fs-16">Total Area</a>
                                <span class="text-fade"><?php echo number_format($project['sellable_area']); ?> sq.ft</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-30">
                            <div class="me-15 bg-success-light h-50 w-50 l-h-60 rounded text-center">
                                <span class="icon-Group-chat fs-24"><span class="path1"></span><span class="path2"></span></span>
                            </div>
                            <div class="d-flex flex-column fw-500">
                                <a href="#" class="text-dark hover-primary mb-1 fs-16">Status</a>
                                <span class="text-fade"><?php echo htmlspecialchars($project['status']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Inventory Overview</h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="bg-primary p-20 rounded">
                                    <h2 class="text-white"><?php echo $availableUnits; ?></h2>
                                    <h5 class="text-white">Available</h5>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-warning p-20 rounded">
                                    <h2 class="text-white"><?php echo $bookedUnits; ?></h2>
                                    <h5 class="text-white">Booked</h5>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-danger p-20 rounded">
                                    <h2 class="text-white"><?php echo $soldUnits; ?></h2>
                                    <h5 class="text-white">Sold</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Rates</h5>
                            <table class="table">
                                <tr>
                                    <td>Res. Ready Reckoner Rate</td>
                                    <td class="text-end">₹ <?php echo number_format($project['ready_reckoner_rate_res'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td>Com. Ready Reckoner Rate</td>
                                    <td class="text-end">₹ <?php echo number_format($project['ready_reckoner_rate_com'], 2); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Recent Activity</h4>
                    </div>
                    <div class="box-body">
                        <p class="text-muted text-center">No recent activity recorded.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>