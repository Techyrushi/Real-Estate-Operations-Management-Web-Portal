<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_vendors')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$stmt = $pdo->query("SELECT * FROM vendors ORDER BY created_at DESC");
$vendors = $stmt->fetchAll();
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Vendor Master</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Vendors</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_vendor_form.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New Vendor</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="vendors_table">
                                <thead>
                                    <tr>
                                        <th>Vendor Name</th>
                                        <th>GST No</th>
                                        <th>Contact</th>
                                        <th>Service Provided</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vendors as $vendor): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($vendor['name'] ?? ''); ?></strong></td>
                                        <td><?php echo htmlspecialchars($vendor['gst_no'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars(substr($vendor['contact_details'] ?? '', 0, 30)); ?>...</td>
                                        <td><?php echo htmlspecialchars(substr($vendor['service_provided'] ?? '', 0, 30)); ?>...</td>
                                        <td>
                                            <?php 
                                            $status = $vendor['status'] ?? 'Active';
                                            $badgeClass = ($status == 'Active') ? 'badge-success' : 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                        </td>
                                        <td>
                                            <a href="admin_vendor_form.php?id=<?php echo $vendor['id']; ?>" class="btn btn-sm btn-info me-1" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
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
