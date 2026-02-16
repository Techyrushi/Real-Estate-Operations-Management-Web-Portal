<?php
include 'includes/header.php';
include 'includes/sidebar.php';

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_projects')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$project_id = $_GET['id'] ?? null;
if (!$project_id) {
    echo "<script>window.location.href='admin_projects.php';</script>";
    exit();
}

// Handle messages and data
$msg = "";
$error = "";
$units = [];
$project = null;

try {
    // Fetch Project Info
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Project Not Found</div></section></div></div>";
        include 'includes/footer.php';
        exit();
    }

    // Handle Unit Add/Update/Delete
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete_unit_id'])) {
            $unitIdToDelete = $_POST['delete_unit_id'];

            // Check if there are any bookings for this unit
            $checkBookings = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE unit_id = ?");
            $checkBookings->execute([$unitIdToDelete]);
            $bookingCount = (int) $checkBookings->fetchColumn();

            if ($bookingCount > 0) {
                $error = "Cannot delete unit because bookings exist for this unit. Cancel or delete bookings first.";
            } else {
                $delStmt = $pdo->prepare("DELETE FROM units WHERE id = ?");
                if ($delStmt->execute([$unitIdToDelete])) {
                    $msg = "Unit deleted successfully.";
                } else {
                    $error = "Failed to delete unit.";
                }
            }
        } else {
            $unit_id = $_POST['unit_id'] ?? '';
            $flat_no = $_POST['flat_no'] ?? '';
            $floor = $_POST['floor'] ?? '';
            $configuration = $_POST['configuration'] ?? '';
            $area = $_POST['area'] ?? '';
            $rate = $_POST['rate'] ?? '';
            $property_type = $_POST['property_type'] ?? '';
            $status = $_POST['status'] ?? 'Available';

            if ($unit_id) {
                $sql = "UPDATE units SET flat_no=?, floor=?, configuration=?, area=?, rate=?, property_type=?, status=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$flat_no, $floor, $configuration, $area, $rate, $property_type, $status, $unit_id])) {  
                    $msg = "Unit updated successfully.";
                } else {
                    $error = "Failed to update unit.";
                }
            } else {
                $sql = "INSERT INTO units (project_id, flat_no, floor, configuration, area, rate, property_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$project_id, $flat_no, $floor, $configuration, $area, $rate, $property_type, $status])) {       
                    $msg = "Unit added successfully.";
                } else {
                    $error = "Failed to add unit.";
                }
            }
        }
    }

    // Fetch Units
    $stmt = $pdo->prepare("SELECT * FROM units WHERE project_id = ? ORDER BY floor ASC, flat_no ASC");
    $stmt->execute([$project_id]);
    $units = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h4 class="page-title">Manage Units: <?php echo htmlspecialchars($project['name']); ?></h4>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a>
                                </li>
                                <li class="breadcrumb-item"><a href="admin_projects.php">Projects</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Units</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#unitModal" onclick="resetForm()"><i class="ti-plus"></i> Add New Unit</button>
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
                                    document.addEventListener('DOMContentLoaded', function () {
                                        var msg = <?php echo json_encode($msg); ?>;
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: msg,
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    });
                                </script>
                            <?php endif; ?>
                            <?php if ($error): ?>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        var err = <?php echo json_encode($error); ?>;
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: err
                                            });
                                        }
                                    });
                                </script>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="units_table">
                                    <thead>
                                        <tr>
                                            <th>Flat No</th>
                                            <th>Floor</th>
                                            <th>Config</th>
                                            <th>Area (sq.ft)</th>
                                            <th>Rate (₹)</th>
                                            <th>Property Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <?php if (!empty($units)): ?>
                                    <tbody>
                                        <?php foreach ($units as $unit): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($unit['flat_no']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($unit['floor']); ?></td>
                                                <td><?php echo htmlspecialchars($unit['configuration']); ?></td>
                                                <td><?php echo number_format($unit['area'], 2); ?></td>
                                                <td>₹ <?php echo number_format($unit['rate'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($unit['property_type']); ?></td>    
                                                <td>
                                                    <?php
                                                    $badgeClass = 'badge-success';
                                                    if ($unit['status'] == 'Booked')
                                                        $badgeClass = 'badge-warning';
                                                    if ($unit['status'] == 'Sold')
                                                        $badgeClass = 'badge-danger';
                                                    ?>
                                                    <span
                                                        class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($unit['status']); ?></span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1"
                                                        onclick='editUnit(<?php echo json_encode($unit, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'><i
                                                            class="ti-pencil"></i></button>
                                                    <form method="post" style="display:inline;" class="delete-form">
                                                        <input type="hidden" name="delete_unit_id"
                                                            value="<?php echo $unit['id']; ?>">
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete(this.form)"><i
                                                                class="ti-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <?php else: ?>
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center fw-bold">No units available.</td>
                                        </tr>
                                    </tbody>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Unit Modal -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unitModalLabel">Add Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post"
                onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                <div class="modal-body">
                    <input type="hidden" name="unit_id" id="unit_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Flat / Unit No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="flat_no" id="flat_no" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Floor</label>
                            <input type="number" class="form-control" name="floor" id="floor" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Configuration</label>
                            <select class="form-select" name="configuration" id="configuration">
                                <option value="1BHK">1BHK</option>
                                <option value="2BHK">2BHK</option>
                                <option value="3BHK">3BHK</option>
                                <option value="4BHK">4BHK</option>
                                <option value="Shop">Shop</option>
                                <option value="Office">Office</option>
                                <option value="Penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Area (sq.ft)</label>
                            <input type="number" step="0.01" class="form-control" name="area" id="area">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rate (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="rate" id="rate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Property Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="property_type" id="property_type" required>   
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="Available">Available</option>
                                <option value="Booked">Booked</option>
                                <option value="Sold">Sold</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('unit_id').value = '';
        document.getElementById('flat_no').value = '';
        document.getElementById('floor').value = '';
        document.getElementById('area').value = '';
        document.getElementById('rate').value = '';
        document.getElementById('unitModalLabel').innerText = 'Add Unit';
    }

    function editUnit(unit) {
        document.getElementById('unit_id').value = unit.id;
        document.getElementById('flat_no').value = unit.flat_no;
        document.getElementById('floor').value = unit.floor;
        document.getElementById('configuration').value = unit.configuration;
        document.getElementById('area').value = unit.area;
        document.getElementById('rate').value = unit.rate;
        document.getElementById('status').value = unit.status;
        document.getElementById('unitModalLabel').innerText = 'Edit Unit';

        var myModal = new bootstrap.Modal(document.getElementById('unitModal'));
        myModal.show();
    }

    function confirmDelete(form) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deleting unit...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                form.submit();
            }
        });
    }
</script>

<?php include 'includes/footer.php'; ?>