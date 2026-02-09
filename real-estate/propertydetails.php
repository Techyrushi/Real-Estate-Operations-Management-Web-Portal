<?php
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT u.*, p.name as project_name, p.address as project_address, p.description as project_description 
                       FROM units u 
                       JOIN projects p ON u.project_id = p.id 
                       WHERE u.id = ?");
$stmt->execute([$id]);
$unit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$unit) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><h4>Property not found.</h4></section></div></div>";
    include 'includes/footer.php';
    exit;
}

$price_formatted = "₹" . number_format($unit['price']);
$title = $unit['unit_number'] . " " . $unit['project_name'];
$address = $unit['project_address'];
$desc = $unit['project_description'];
$area = $unit['area'] . " SqFt";
$bedrooms = $unit['bedrooms'];
$bathrooms = $unit['bathrooms'];
$type = $unit['type'];
$status = $unit['status'];
$image_path = "../images/property/" . ($unit['image'] ? $unit['image'] : 'p1.jpg');
?>
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-lg-8 col-12">
					<div class="box">
						<div class="box-body">				  
							<div class="flexslider2">
							  <ul class="slides">
								<li data-thumb="<?php echo $image_path; ?>">
								  <img src="<?php echo $image_path; ?>" alt="slide" />
								</li>
								<li data-thumb="<?php echo $image_path; ?>">
								  <img src="<?php echo $image_path; ?>" alt="slide" />
								</li>
								<li data-thumb="<?php echo $image_path; ?>">
								  <img src="<?php echo $image_path; ?>" alt="slide" />
								</li>
							  </ul>
							</div>
						</div>
					</div>  
					<div class="box">
						<div class="box-body">
							<div class="property-bx p-20">
								<div>
									<h5 class="text-success mt-0 mb-20"><?php echo $price_formatted; ?> <span class="text-muted font-size-14">For <?php echo $status; ?></span></h5>
									<h3 class="mt-0"><a href="#" class="text-primary"><?php echo $title; ?></a></h3>
									<p class="text-muted"><i class="mdi mdi-pin me-5"></i><?php echo $address; ?></p>
									<p class="text-muted mb-0"><?php echo $desc; ?></p>
								</div>
								<div class="mt-15 fs-18">
									<a href="#" title="Square Feet" class="me-15"><i class="mdi mdi-view-dashboard me-10"></i><span><?php echo $area; ?></span></a>
									<a href="#" title="Bedroom" class="me-15"><i class="mdi mdi-hotel me-10"></i><span><?php echo $bedrooms; ?></span></a>
									<a href="#" title="Bathroom" class="me-15"><i class="mdi mdi-scale-bathroom me-10"></i><span><?php echo $bathrooms; ?></span></a>
									<a href="#" title="Type" class="me-15"><i class="mdi mdi-home me-10"></i><span> <?php echo $type; ?></span></a>
								</div>
							</div>
						</div>
					</div>
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">General Amenities</h4>
							<ul class="box-controls pull-right">
							  <li class="dropdown">
								<a data-toggle="dropdown" href="#" class="px-10 hover-primary"><i class="ti-menu hover-primary"></i></a>
								<div class="dropdown-menu dropdown-menu-right">
								  <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
								  <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
								  <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
								  <div class="dropdown-divider"></div>
								  <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
								</div>
							  </li>
							</ul>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-sm-4">
									<ul class="list-unstyled">
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Swimming pool</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Air conditioning</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Internet</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Radio</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Balcony</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Roof terrace</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Cable TV</li>
									<li><i class="mdi mdi-check-circle text-success me-5"></i>Electricity</li>
								</ul>
								</div>
								<div class="col-sm-4">
									<ul class="list-unstyled proprerty-features">
										<li><i class="mdi mdi-star text-warning me-5"></i>Terrace</li>
										<li><i class="mdi mdi-star text-warning me-5"></i>Cofee pot</li>
										<li><i class="mdi mdi-star text-warning me-5"></i>Oven</li>
										<li><i class="mdi mdi-star text-warning me-5"></i>Towelwes</li>
										<li><i class="mdi mdi-star text-warning me-5"></i>Computer</li>
										<li><i class="mdi mdi-star text-warning me-5"></i>Grill</li>
										<li><i class="mdi mdi-star text-warning me-5"></i>Parquet</li>
									</ul>
								</div>
								<div class="col-sm-4">
									<ul class="list-unstyled proprerty-features">
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Dishwasher</li>
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Near Green Zone</li>
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Near Church</li>
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Near Hospital</li>
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Near School</li>
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Near Shop</li>
										<li><i class="mdi mdi-check-circle text-info me-5"></i>Natural Gas</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Location</h4>
							<ul class="box-controls pull-right">
							  <li class="dropdown">
								<a data-toggle="dropdown" href="#" class="px-10 hover-primary"><i class="ti-menu hover-primary"></i></a>
								<div class="dropdown-menu dropdown-menu-right">
								  <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
								  <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
								  <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
								  <div class="dropdown-divider"></div>
								  <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
								</div>
							  </li>
							</ul>
						</div>
						<div class="box-body p-0">
							<iframe src="https://www.google.com/maps/d/embed?mid=1NOpS0xArrU98SXwC-OMiv2JZvmE&amp;hl=en" class="w-p100 h-450 no-border"></iframe>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-12">
					<div class="box">
						<div class="box-body">
							<div class="d-flex flex-row">
								<div class=""><img src="../images/avatar/1.jpg" alt="user" class="rounded-circle" width="100"></div>
								<div class="ps-20">
									<h3>Johen Doe</h3>
									<h6>120 lorem Ave, Suite 125 Miami, FL 123546</h6>
									<button class="btn btn-success"><i class="ti-plus"></i> Contact</button>
								</div>
							</div>
							<div class="row mt-40">
								<div class="col text-center">
									<h2 class="font-light">14</h2>
									<h6>Files</h6>
								</div>
								<div class="col text-center">
									<h2 class="font-light">12GB</h2>
									<h6>Used</h6>
								</div>
								<div class="col text-center">
									<h2 class="font-light">₹25k</h2>
									<h6>Spent</h6>
								</div>
							</div>
						</div>
						<div class="box-body">
							<p class="text-center">
								Simple text dolor sit amet, consectetur adipiscing elit. Duis pharetra varius quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae.
							</p>
							<ul class="list-inline text-center">
								<li><a href="javascript:void(0)"><i class="fa fa-instagram fs-20"></i></a></li>
								<li><a href="javascript:void(0)"><i class="fa-brands fa-x-twitter fs-20"></i></a></li>
								<li><a href="javascript:void(0)"><i class="fa fa-facebook-square fs-20"></i></a></li>
							</ul>
						</div>
					</div>
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Request Inquiry</h4>
							<ul class="box-controls pull-right">
							  <li class="dropdown">
								<a data-bs-toggle="dropdown" href="#" class="px-10 hover-primary"><i class="ti-menu hover-primary"></i></a>
								<div class="dropdown-menu">
								  <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
								  <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
								  <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
								  <div class="dropdown-divider"></div>
								  <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
								</div>
							  </li>
							</ul>
						</div>
						<div class="box-body">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Name">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Phone Number">
							</div>
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Email">
							</div>
							<div class="form-group">
								<textarea rows="4" class="form-control no-resize" placeholder="Please type what you want..."></textarea>
							</div>
						</div>
						<div class="box-footer">
							<button type="submit" class="btn btn-primary me-1 waves-effect waves-light">
							  <i class="ti-save-alt"></i> Submit
							</button>
							<button type="button" class="btn btn-danger waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
						</div>
					</div>
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Property Overview</h4>
							<ul class="box-controls pull-right">
							  <li class="dropdown">
								<a data-bs-toggle="dropdown" href="#" class="px-10 hover-primary"><i class="ti-menu hover-primary"></i></a>
								<div class="dropdown-menu">
								  <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
								  <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
								  <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
								  <div class="dropdown-divider"></div>
								  <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
								</div>
							  </li>
							</ul>
						</div>
						<div class="box-body">
							<div class="table-responsive">
								<table class="table table-bordered mb-0">
									<tbody>
										<tr>
											<th scope="row">Price:</th>
											<td><?php echo $price_formatted; ?></td>
										</tr>
										<tr>
											<th scope="row">Contract type: </th>
											<td><span class="badge badge-primary">For <?php echo $status; ?></span></td>
										</tr>
										<tr>
											<th scope="row">Bathrooms:</th>
											<td><?php echo $bathrooms; ?></td>
										</tr>
										<tr>
											<th scope="row">Square ft:</th>
											<td><?php echo $area; ?></td>
										</tr>
										<tr>
											<th scope="row">Garage Spaces:</th>
											<td>2</td>
										</tr>
										<tr>
											<th scope="row">Land Size:</th>
											<td>N/A</td>
										</tr>
										<tr>
											<th scope="row">Floors:</th>
											<td>N/A</td>
										</tr>
										<tr>
											<th scope="row">Listed for:</th>
											<td>15 days</td>
										</tr>
										<tr>
											<th scope="row">Available:</th>
											<td>Immediately</td>
										</tr>
										<tr>
											<th scope="row">Pets:</th>
											<td>Pets Allowed</td>
										</tr>
										<tr>
											<th scope="row">Bedrooms:</th>
											<td><?php echo $bedrooms; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /.content -->
	  </div>
  </div>
  
<!-- /.content-wrapper -->
<?php
$hide_dashboard_js = true;
$extra_js = "<script src='../assets/vendor_components/flexslider/jquery.flexslider.js'></script>
<script>
    .flexslider2.flexslider({
        animation: 'slide',
        controlNav: 'thumbnails'
    });
</script>";
include 'includes/footer.php';
?>
