<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
include 'config/db.php';

// Fetch filter options dynamically
$cities = $pdo->query("SELECT DISTINCT city FROM projects WHERE city IS NOT NULL ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
$states = $pdo->query("SELECT DISTINCT state FROM projects WHERE state IS NOT NULL ORDER BY state")->fetchAll(PDO::FETCH_COLUMN);
$prop_types = $pdo->query("SELECT DISTINCT property_type FROM units WHERE property_type IS NOT NULL ORDER BY property_type")->fetchAll(PDO::FETCH_COLUMN);
// If no types yet, provide defaults
if (empty($prop_types)) $prop_types = ['Apartment', 'Villa', 'Office', 'Shop', 'Plot'];

// Build Query
$sql = "SELECT u.*, p.name as project_name, p.address as project_address, p.city, p.state, p.image as project_image 
        FROM units u 
        JOIN projects p ON u.project_id = p.id 
        WHERE 1=1";

$params = [];

// Apply Filters
if (!empty($_GET['status'])) {
    $sql .= " AND u.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['type'])) {
    $sql .= " AND u.property_type = ?";
    $params[] = $_GET['type'];
}

if (!empty($_GET['city'])) {
    $sql .= " AND p.city = ?";
    $params[] = $_GET['city'];
}

if (!empty($_GET['state'])) {
    $sql .= " AND p.state = ?";
    $params[] = $_GET['state'];
}

if (!empty($_GET['bedrooms'])) {
    $sql .= " AND u.bedrooms >= ?";
    $params[] = $_GET['bedrooms'];
}

if (!empty($_GET['bathrooms'])) {
    $sql .= " AND u.bathrooms >= ?";
    $params[] = $_GET['bathrooms'];
}

if (!empty($_GET['min_area'])) {
    $sql .= " AND u.area >= ?";
    $params[] = $_GET['min_area'];
}

if (!empty($_GET['max_price'])) {
    // Assuming price is stored or calculated. Let's use rate * area for calculation if price column not present, 
    // but better to rely on what's there. 
    // If 'price' column exists, use it. If not, use rate * area.
    // For SQL WHERE, we need to know. 
    // Let's assume 'rate' is the main price factor.
    // Let's filter by rate (budget per sqft) or total package? usually total package.
    // (u.rate * u.area) <= ?
    $sql .= " AND (u.rate * u.area) <= ?";
    $params[] = $_GET['max_price'];
}

$sql .= " ORDER BY u.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$units = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">            
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Advanced Search</h4>
                        </div>
                        <div class="box-body">
                            <form method="GET" action="propertygrid.php">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <select class="form-control select2" name="status" style="width: 100%;">
                                                <option value="">All Statuses</option>
                                                <option value="Available" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                                <option value="Booked" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Booked') ? 'selected' : ''; ?>>Booked</option>
                                                <option value="Sold" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Sold') ? 'selected' : ''; ?>>Sold</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Property Type</label>
                                            <select class="form-control select2" name="type" style="width: 100%;">
                                                <option value="">All Types</option>
                                                <?php foreach ($prop_types as $type): ?>
                                                    <option value="<?php echo $type; ?>" <?php echo (isset($_GET['type']) && $_GET['type'] == $type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">City</label>
                                            <select class="form-control select2" name="city" style="width: 100%;">
                                                <option value="">All Cities</option>
                                                <?php foreach ($cities as $city): ?>
                                                    <option value="<?php echo $city; ?>" <?php echo (isset($_GET['city']) && $_GET['city'] == $city) ? 'selected' : ''; ?>><?php echo $city; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Bedrooms (Min)</label>
                                            <select class="form-control select2" name="bedrooms" style="width: 100%;">
                                                <option value="">Any</option>
                                                <option value="1" <?php echo (isset($_GET['bedrooms']) && $_GET['bedrooms'] == '1') ? 'selected' : ''; ?>>1+</option>
                                                <option value="2" <?php echo (isset($_GET['bedrooms']) && $_GET['bedrooms'] == '2') ? 'selected' : ''; ?>>2+</option>
                                                <option value="3" <?php echo (isset($_GET['bedrooms']) && $_GET['bedrooms'] == '3') ? 'selected' : ''; ?>>3+</option>
                                                <option value="4" <?php echo (isset($_GET['bedrooms']) && $_GET['bedrooms'] == '4') ? 'selected' : ''; ?>>4+</option>
                                                <option value="5" <?php echo (isset($_GET['bedrooms']) && $_GET['bedrooms'] == '5') ? 'selected' : ''; ?>>5+</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Bathrooms (Min)</label>
                                            <select class="form-control select2" name="bathrooms" style="width: 100%;">
                                                <option value="">Any</option>
                                                <option value="1" <?php echo (isset($_GET['bathrooms']) && $_GET['bathrooms'] == '1') ? 'selected' : ''; ?>>1+</option>
                                                <option value="2" <?php echo (isset($_GET['bathrooms']) && $_GET['bathrooms'] == '2') ? 'selected' : ''; ?>>2+</option>
                                                <option value="3" <?php echo (isset($_GET['bathrooms']) && $_GET['bathrooms'] == '3') ? 'selected' : ''; ?>>3+</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Min Area (SqFt)</label>
                                            <input type="number" class="form-control" name="min_area" placeholder="e.g. 1000" value="<?php echo $_GET['min_area'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Max Budget (₹)</label>
                                            <input type="number" class="form-control" name="max_price" placeholder="e.g. 5000000" value="<?php echo $_GET['max_price'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-p100"><i class="ti-search"></i> Search Properties</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <?php if (count($units) > 0): ?>
                    <?php foreach ($units as $unit): 
                        // Image handling
                        $img_src = !empty($unit['image']) ? "../images/property/" . $unit['image'] : 
                                  (!empty($unit['project_image']) ? "../images/property/" . $unit['project_image'] : '../images/property/p1.jpg');
                        
                        // Price calculation
                        $price = $unit['rate'] * $unit['area'];
                        $price_formatted = "₹" . number_format($price);
                        
                        $title = $unit['property_type'] . " " . $unit['flat_no'];
                        $address = $unit['project_address'];
                        $desc = !empty($unit['description']) ? substr($unit['description'], 0, 80) . "..." : $unit['project_name'];
                        $area = $unit['area'] . " SqFt";
                        $bedrooms = $unit['bedrooms'];
                        $bathrooms = $unit['bathrooms'];
                        $status_badge = match($unit['status']) {
                            'Available' => 'badge-success',
                            'Booked' => 'badge-warning',
                            'Sold' => 'badge-danger',
                            default => 'badge-primary'
                        };
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                        <div class="box">
                            <div class="box-body p-0">
                                <div class="position-relative">
                                    <img class="img-fluid" src="<?php echo $img_src; ?>" alt="img" style="width: 100%; height: 200px; object-fit: cover;">
                                    <span class="badge <?php echo $status_badge; ?> position-absolute top-0 end-0 m-10"><?php echo $unit['status']; ?></span>
                                </div>
                                <div class="property-bx p-20">
                                    <div>
                                        <h5 class="text-success mt-0 mb-10"><?php echo $price_formatted; ?></h5>
                                        <h4 class="mt-0 mb-10"><a href="propertydetails.php?id=<?php echo $unit['id']; ?>" class="text-primary"><?php echo $title; ?></a></h4>
                                        <p class="text-muted fs-12 mb-10"><i class="mdi mdi-map-marker me-5"></i><?php echo $unit['project_name'] . ", " . $unit['city']; ?></p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-15 pt-15 border-top">
                                        <span title="Area"><i class="mdi mdi-view-dashboard me-5"></i><?php echo $area; ?></span>
                                        <span title="Bedrooms"><i class="mdi mdi-hotel me-5"></i><?php echo $bedrooms; ?></span>
                                        <span title="Bathrooms"><i class="mdi mdi-shower me-5"></i><?php echo $bathrooms; ?></span>
                                    </div>
                                    <div class="mt-15 text-center">
                                        <a href="propertydetails.php?id=<?php echo $unit['id']; ?>" class="btn btn-primary btn-sm w-p100">View Details</a>
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
                                <h3 class="mt-20">No properties found matching your criteria.</h3>
                                <a href="propertygrid.php" class="btn btn-info mt-10">Clear Filters</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <!-- /.content -->
    </div>
</div>
<!-- /.content-wrapper -->

<?php
$hide_dashboard_js = true;
$extra_js = '<script src="../assets/vendor_components/select2/dist/js/select2.full.js"></script>
<script>
    $(document).ready(function() {
        $(".select2").select2();
    });
</script>';
include 'includes/footer.php';
?>
