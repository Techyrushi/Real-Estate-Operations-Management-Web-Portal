<?php include 'includes/header.php'; ?>
  
<?php include 'includes/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Basic Information</h4>
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
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Property Name">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Property Location">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group mb-0">
										<div class="form-line">
											<textarea rows="4" class="form-control no-resize" placeholder="Property Description"></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger me-1 waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
							<button type="submit" class="btn btn-primary waves-effect waves-light">
							  <i class="ti-save-alt"></i> Save
							</button>
						</div>
					</div>
				</div>
				<div class="col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Property For</h4>
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
							<div class="row">
								<div class="col-sm-6">
									<div class="d-flex">
										<div class="radio me-25">
											<input type="radio" name="radio1" id="radio1" value="option1" checked="">
											<label for="radio1">For Rent</label>
										</div>
										<div class="radio">
											<input type="radio" name="radio1" id="radio2" value="option2">
											<label for="radio2">For Sale</label>
										</div>									
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Price / Rent">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<textarea rows="4" class="form-control no-resize" placeholder="Property Address"></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3 col-md-6">
									<div class="form-group mb-lg-0">
										<input type="text" class="form-control" placeholder="Bedrooms">
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="form-group mb-lg-0">
										<input type="text" class="form-control" placeholder="Square ft">
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="form-group mb-lg-0">
										<input type="text" class="form-control" placeholder="Car Parking">
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="form-group mb-0">
										<input type="text" class="form-control" placeholder="Year Built">
									</div>
								</div>
							</div>  
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger me-1 waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
							<button type="submit" class="btn btn-primary waves-effect waves-light">
							  <i class="ti-save-alt"></i> Save
							</button>
						</div>
					</div>
				</div>
				<div class="col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Dimensions</h4>
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
							<div class="row">
								<div class="col-lg-2 col-md-4 col-12">
									<div class="form-line mb-lg-0">
										<input type="text" class="form-control" placeholder="Dining Room">
									</div>
								</div>
								<div class="col-lg-2 col-md-4 col-12">
									<div class="form-line mb-lg-0">
										<input type="text" class="form-control" placeholder="Kitchen">
									</div>
								</div>
								<div class="col-lg-2 col-md-4 col-12">
									<div class="form-line mb-lg-0">
										<input type="text" class="form-control" placeholder="Living Room">
									</div>
								</div>
								<div class="col-lg-2 col-md-4 col-12">
									<div class="form-group mb-lg-0">
										<input type="text" class="form-control" placeholder="Master Bedroom">
									</div>
								</div>
								<div class="col-lg-2 col-md-4 col-12">
									<div class="form-group mb-lg-0">
										<input type="text" class="form-control" placeholder="Bedroom 2">
									</div>
								</div>
								<div class="col-lg-2 col-md-4 col-12">
									<div class="form-group mb-0">
										<input type="text" class="form-control" placeholder="Other Room">
									</div>
								</div>
							</div>							  
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger me-1 waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
							<button type="submit" class="btn btn-primary waves-effect waves-light">
							  <i class="ti-save-alt"></i> Save
							</button>
						</div>
					</div>
				</div>
				<div class="col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">General Amenities</h4>
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
							<div class="row">
								<div class="col-sm-12">
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox1" type="checkbox">
										<label for="checkbox1">Swimming pool</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox2" type="checkbox">
										<label for="checkbox2">Terrace</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox3" type="checkbox" checked="">
										<label for="checkbox3">Air conditioning</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox4" type="checkbox" checked="">
										<label for="checkbox4">Internet</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox5" type="checkbox">
										<label for="checkbox5">Balcony</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox6" type="checkbox">
										<label for="checkbox6">Cable TV</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox7" type="checkbox">
										<label for="checkbox7">Computer</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox8" type="checkbox" checked="">
										<label for="checkbox8">Dishwasher</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox9" type="checkbox" checked="">
										<label for="checkbox9">Near Green Zone</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox0" type="checkbox">
										<label for="checkbox0">Near Church</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox11" type="checkbox">
										<label for="checkbox11">Near Estate</label>
									</div>
									<div class="checkbox d-inline-block me-20">
										<input id="checkbox12" type="checkbox">
										<label for="checkbox12">Cofee pot</label>
									</div>
								</div>
							</div>
							<div class="row clearfix">                            
								<div class="col-sm-12">
									<form action="https://master-admin-template.multipurposethemes.com/" id="frmFileUpload" class="dropzone mt-15 dz-clickable" method="post">
										<div class="dz-message">
											<div> <i class="mdi mdi-cursor-pointer fs-36"></i> </div>
											<h3>Drop files here or click to upload.</h3>
											<em>(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</em>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger me-1 waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
							<button type="submit" class="btn btn-primary waves-effect waves-light">
							  <i class="ti-save-alt"></i> Submit
							</button>
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
$extra_js = '<script src="../assets/vendor_components/dropzone/dropzone.js"></script>';
include 'includes/footer.php';
?>


