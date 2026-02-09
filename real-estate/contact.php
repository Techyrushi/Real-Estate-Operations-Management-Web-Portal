<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="content-wrapper">
	  <div class="container">
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">How can we help?</h4>
				</div>
				
			</div>
		</div>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-12">
					<div class="box">
						<div class="box-header with-border">
						  <h6 class="box-title">You can find all of the questions and answers abour secure your account</h6>
						</div>
						<form class="form">
							<div class="box-body">
								<h5 class="box-title mb-0 text-info"><i class="ti-user me-15"></i> Personal Info</h5>
								<hr class="my-15">
								<div class="row">
								  <div class="col-md-6">
									<div class="form-group">
									  <label class="form-label">First Name</label>
									  <input type="text" class="form-control" placeholder="First Name">
									</div>
								  </div>
								  <div class="col-md-6">
									<div class="form-group">
									  <label class="form-label">Last Name</label>
									  <input type="text" class="form-control" placeholder="Last Name">
									</div>
								  </div>
								</div>
								<div class="row">
								  <div class="col-md-6">
									<div class="form-group">
									  <label class="form-label">E-mail</label>
									  <input type="text" class="form-control" placeholder="E-mail">
									</div>
								  </div>
								  <div class="col-md-6">
									<div class="form-group">
									  <label class="form-label">Contact Number</label>
									  <input type="text" class="form-control" placeholder="Phone">
									</div>
								  </div>
								</div>
								<h5 class="box-title text-info mb-0 mt-20"><i class="ti-file me-15"></i> Give us the details </h5>
								<hr class="my-15">								
								<div class="form-group">
								  <label class="form-label">Select File</label>
								  <label class="file">
									<input type="file" id="file">
								  </label>
								</div>
								<div class="form-group">
								  <label class="form-label">Message</label>
								  <textarea rows="5" class="form-control" placeholder="Write your message"></textarea>
								</div>
							</div>
							<div class="box-footer">
								<button type="submit" class="btn btn-primary">
								  <i class="fa fa-paper-plane"></i> Submit
								</button>
							</div>  
						</form>
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
include 'includes/footer.php';
?>
