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
								<div class="col-sm-4">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="First Name">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Last Name">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Phone No.">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Date of Birth">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Age">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<select class="form-select select2">
										  <option selected="selected">Select Gender</option>
										  <option>Male</option>
										  <option>Female</option>
										</select>
								    </div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Enter Your Email">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 mb-20">
									<form action="https://master-admin-template.multipurposethemes.com/" id="frmFileUpload" class="dropzone mt-15 dz-clickable" method="post">
										<div class="dz-message">
											<div> <i class="mdi mdi-cursor-pointer fs-36"></i> </div>
											<h3>Drop files here or click to upload.</h3>
											<em>(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</em>
										</div>
									</form>
								</div>
								<div class="col-12">
									<div class="form-group mb-0">
										<textarea rows="4" class="form-control no-resize" placeholder="Description"></textarea>
									</div>
								</div>
							</div>							
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger mr-1 waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
							<button type="submit" class="btn btn-primary waves-effect waves-light">
							  <i class="ti-save-alt"></i> Submit
							</button>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Account Information</h4>
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
										<input type="text" class="form-control" placeholder="Email">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Phone">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group mb-md-0">
										<input type="text" class="form-control" placeholder="Password">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group mb-0">
										<input type="text" class="form-control" placeholder="Confirm Password">
									</div>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger mr-1 waves-effect waves-light">
							  <i class="ti-trash"></i> Cancel
							</button>
							<button type="submit" class="btn btn-primary waves-effect waves-light">
							  <i class="ti-save-alt"></i> Submit
							</button>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Social Information</h4>
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
										<input type="text" class="form-control" placeholder="Facebook">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" class="form-control" placeholder="Twitter">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group mb-md-0">
										<input type="text" class="form-control" placeholder="Instagram">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group mb-0">
										<input type="text" class="form-control" placeholder="Email">
									</div>
								</div>
							</div>
						</div>
						<div class="box-footer">
							<button type="button" class="btn btn-danger mr-1 waves-effect waves-light">
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


