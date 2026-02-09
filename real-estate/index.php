<?php
include 'includes/header.php';
include 'includes/sidebar.php'
	?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="container-full">
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
						<div class="box-body">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="fw-700 mt-0">4,789<span class="text-muted"><small> / month</small></span>
								</h3>
								<div class="text-danger fw-700 d-flex justify-content-between align-items-center">
									<i class="mdi mdi-chevron-down mdi-24px"></i> <span><small>58.7%</small></span>
								</div>
							</div>
							<h4 class="text-primary">Total Income</h4>
							<canvas id="customer"></canvas>
							<p class="mb-5">+14.17% last month</p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
						<div class="box-body">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="fw-700 mt-0">8,695<span class="text-muted"><small> / month</small></span>
								</h3>
								<div class="text-success fw-700 d-flex justify-content-between align-items-center">
									<i class="mdi mdi-chevron-up mdi-24px"></i> <span><small>97.5%</small></span>
								</div>
							</div>
							<h4 class="text-primary">Total Visitor</h4>
							<canvas id="orders"></canvas>
							<p class="mb-5">-5.18% last month</p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
						<div class="box-body">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="fw-700 mt-0">78%<span class="text-muted"><small> / month</small></span></h3>
								<div class="text-success fw-700 d-flex justify-content-between align-items-center">
									<i class="mdi mdi-chevron-up mdi-24px"></i> <span><small>89.13%</small></span>
								</div>
							</div>
							<h4 class="text-primary">Total Booking</h4>
							<canvas id="growth"></canvas>
							<p class="mb-5">+12.2% last month</p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
						<div class="box-body">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="fw-700 mt-0">$ 42k<span class="text-muted"><small> / month</small></span>
								</h3>
								<div class="text-danger fw-700 d-flex justify-content-between align-items-center">
									<i class="mdi mdi-chevron-down mdi-24px"></i> <span><small>56.48%</small></span>
								</div>
							</div>
							<h4 class="text-primary">Revenue</h4>
							<canvas id="revenue"></canvas>
							<p class="mb-5">+11.00% last month</p>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Daily Inquery</h4>
						</div>
						<div class="box-body">
							<div id="flotPie2" style="height: 285px;"></div>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Booking Status</h4>
						</div>
						<div class="box-body">
							<div id="bookingstatus"></div>
						</div>
					</div>
				</div>
				<div class="col-12 col-xl-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Revenue</h4>
						</div>
						<div class="box-body">
							<div id="revenue2"></div>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-12">
					<div class="row">
						<div class="col-md-6 col-12">
							<div class="box box-body pb-10 bs-4 border-info pull-up">
								<h6 class="text-uppercase">ON GOING</h6>
								<div class="d-flex justify-content-between">
									<span class=" fs-30">154</span>
									<span class="fs-30 text-info mdi mdi-city"></span>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="box box-body pb-10 bs-4 border-primary pull-up">
								<h6 class="text-uppercase">COMPLATE</h6>
								<div class="d-flex justify-content-between">
									<span class=" fs-30">412</span>
									<span class="fs-30 text-primary mdi mdi-seal"></span>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="box box-body pb-10 bs-4 border-danger pull-up">
								<h6 class="text-uppercase">COMMERCIAL</h6>
								<div class="d-flex justify-content-between">
									<span class=" fs-30">125</span>
									<span class="fs-30 text-danger mdi mdi-city"></span>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="box box-body pb-10 bs-4 border-warning pull-up">
								<h6 class="text-uppercase">RESIDENTIAL</h6>
								<div class="d-flex justify-content-between">
									<span class=" fs-30">256</span>
									<span class="fs-30 text-warning mdi mdi-home"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="box box-widget widget-user-4">
						<div class="widget-user-header" style="background: url('../images/photo1.png') center center;">
							<div class="overlay dark">
								<div class="widget-user-image">
									<img class="rounded-circle" src="../images/user1-128x128.jpg" alt="User Avatar">
								</div>
								<h3 class="widget-user-username text-white">James Anderson</h3>
								<h6 class="widget-user-desc text-white">Top Agent </h6>
							</div>
						</div>

						<div class="box-footer">
							<div class="row">
								<div class="col-sm-4 border-start">
									<div class="description-block">
										<h5 class="description-header">12K</h5>
										<span class="description-text">Total</span>
									</div>
								</div>
								<div class="col-sm-4 border-start">
									<div class="description-block">
										<h5 class="description-header">550</h5>
										<span class="description-text">Projects</span>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="description-block">
										<h5 class="description-header">1158</h5>
										<span class="description-text">Target</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-8 col-12">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Property Overview</h4>
						</div>
						<div class="box-body pt-10">
							<div class="table-responsive">
								<table class="table product-overview mb-0">
									<thead>
										<tr>
											<th>Customer</th>
											<th>Order ID</th>
											<th>Property</th>
											<th>Type</th>
											<th>Date</th>
											<th>Status</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Minol Jov</td>
											<td>#8457125</td>
											<td>Shop</td>
											<td>Sold</td>
											<td>10-7-2019</td>
											<td> <span class="label label-success">Paid</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Adem Kalp</td>
											<td>#96523154</td>
											<td>Shop</td>
											<td>On Rent</td>
											<td>09-7-2019</td>
											<td> <span class="label label-warning">Pending</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Mical Don</td>
											<td>#95487512</td>
											<td>Dupelx</td>
											<td>On Rent</td>
											<td>08-7-2019</td>
											<td> <span class="label label-success">Paid</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Johen Doe</td>
											<td>#75485426</td>
											<td>Shop</td>
											<td>Sold</td>
											<td>02-7-2019</td>
											<td> <span class="label label-danger">Failed</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Minol Jov</td>
											<td>#8457125</td>
											<td>Shop</td>
											<td>Sold</td>
											<td>10-7-2019</td>
											<td> <span class="label label-success">Paid</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Adem Kalp</td>
											<td>#96523154</td>
											<td>Shop</td>
											<td>On Rent</td>
											<td>09-7-2019</td>
											<td> <span class="label label-warning">Pending</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Mical Don</td>
											<td>#95487512</td>
											<td>Dupelx</td>
											<td>On Rent</td>
											<td>08-7-2019</td>
											<td> <span class="label label-success">Paid</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
										</tr>
										<tr>
											<td>Johen Doe</td>
											<td>#75485426</td>
											<td>Shop</td>
											<td>Sold</td>
											<td>02-7-2019</td>
											<td> <span class="label label-danger">Failed</span> </td>
											<td><a href="javascript:void(0)" class="text-dark pe-10"
													data-bs-toggle="tooltip" title="Edit"><i
														class="ti-marker-alt"></i></a>
												<a href="javascript:void(0)" class="text-dark" data-bs-toggle="tooltip"
													title="Delete"><i class="ti-trash"></i></a>
											</td>
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
<?php include 'includes/footer.php'; ?>