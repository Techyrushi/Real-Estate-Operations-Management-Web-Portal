<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-xl-3 col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Search</h4>
						</div>
						<div class="box-body">
						  <div class="form-group">
							<select class="form-control select2" style="width: 100%;">
							  <option selected="selected">Select Property</option>
							  <option>For Sale</option>
							  <option>For Rent</option>
							</select>
						  </div>
						  <div class="form-group">
							<select class="form-control select2" style="width: 100%;">
							  <option selected="selected">Property Type</option>
							  <option>Apartments</option>
							  <option>Houses</option>
							  <option>Commercial</option>
							  <option>Garages</option>
							  <option>Plots</option>
							</select>
						  </div>
						  <div class="form-group">
							<select class="form-control select2" style="width: 100%;">
							  <option selected="selected">Select States</option>
							  <option>Alaska</option>
							  <option>California</option>
							  <option>Colorado</option>
							</select>
						  </div>
						  <div class="form-group">
							<select class="form-control select2" style="width: 100%;">
							  <option selected="selected">Select City</option>
							  <option>New York</option>
							  <option>Los Angeles</option>
							  <option>Chicago</option>
							  <option>Houston</option>
							  <option>Phoenix</option>
							  <option>San Antonio</option>
							  <option>Queens</option>
							</select>
						  </div>
						  <div class="form-group">
							<select class="form-control select2" style="width: 100%;">
							  <option selected="selected">Bed Room</option>
							  <option>1</option>
							  <option>2</option>
							  <option>3</option>
							  <option>4</option>
							  <option>5</option>
							</select>
						  </div>
						  <div class="form-group">
							<select class="form-control select2" style="width: 100%;">
							  <option selected="selected">Bath Room</option>
							  <option>1</option>
							  <option>2</option>
							  <option>3</option>
							  <option>4</option>
							  <option>5</option>
							</select>
						  </div>
						  <div class="form-group">
							<input type="text" class="form-control" placeholder="Area Range">
						  </div>
						  <div class="form-group">
							<input type="text" class="form-control" placeholder="Price Range">
						  </div>
						  <div class="form-group">
							<button type="submit" class="btn btn-rounded btn-info">Search</button>
						  </div>
						</div>
					</div>
				</div>
				<div class="col-xl-9 col-12">
					<?php
					include 'config/db.php';
					
					// Fetch units with project details
					$stmt = $pdo->query("SELECT u.*, p.name as project_name, p.address as project_address, p.description as project_description 
										 FROM units u 
										 JOIN projects p ON u.project_id = p.id 
										 ORDER BY u.id DESC");
					$units = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					if (count($units) > 0) {
						foreach ($units as $unit) {
							$image_path = "../images/property/" . ($unit['image'] ? $unit['image'] : 'p1.jpg');
							$price_formatted = "₹" . number_format($unit['price']);
							$title = $unit['unit_number'] . " " . $unit['project_name'];
							$address = $unit['project_address'];
							$desc = substr($unit['project_description'], 0, 150) . "...";
							$area = $unit['area'] . " SqFt";
							$bedrooms = $unit['bedrooms'];
							$bathrooms = $unit['bathrooms'];
							$type = $unit['type'];
							
							?>
							<div class="box">
								<div class="box-body">
									<div class="row">
										<div class="col-lg-4 col-12">
											<img class="img-thumbnail img-fluid" src="<?php echo $image_path; ?>" alt="img" style="width: 100%; height: 250px; object-fit: cover;">
										</div>
										<div class="col-lg-8 col-12">
											<div class="property-bx p-20">
												<div>
													<h5 class="text-success mt-0 mb-20"><?php echo $price_formatted; ?> <span class="text-muted font-size-14">For <?php echo $unit['status']; ?></span></h5>
													<h3 class="mt-0"><a href="propertydetails.php?id=<?php echo $unit['id']; ?>" class="text-primary"><?php echo $title; ?></a></h3>
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
								</div>
							</div>
							<?php
						}
					} else {
						echo '<div class="box"><div class="box-body"><h4>No properties found.</h4></div></div>';
					}
					?>
					
					<nav aria-label="Page navigation example">
					  <ul class="pagination justify-content-center">
						<li class="page-item disabled">
						  <a class="page-link" href="#" tabindex="-1">Previous</a>
						</li>
						<li class="page-item"><a class="page-link" href="#">1</a></li>
						<li class="page-item"><a class="page-link" href="#">2</a></li>
						<li class="page-item"><a class="page-link" href="#">3</a></li>
						<li class="page-item">
						  <a class="page-link" href="#">Next</a>
						</li>
					  </ul>
					</nav>
				</div>
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
    //Initialize Select2 Elements
    $(".select2").select2();
</script>';
include 'includes/footer.php';
?>
