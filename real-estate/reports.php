<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-xl-7 col-lg-6 col-12">
					<div class="box">
						<div class="box-header">
						  <h4 class="box-title">Resent Inquery</h4>
							<ul class="box-controls pull-right">
							  <li><a class="box-btn-close" href="#"></a></li>
							  <li><a class="box-btn-slide" href="#"></a></li>	
							  <li><a class="box-btn-fullscreen" href="#"></a></li>
							</ul>
						</div>
						<div class="box-body">
							<div class="table-responsive">
							  <table class="table mb-0">
								<tbody>
									<tr>
									  <th>#</th>
									  <th>Name</th>
									  <th>Property Type</th>
									  <th>Date</th>
									  <th>Status</th>
									  <th>Contact Number</th>
									</tr>
									<tr>
									  <td>1.</td>
									  <td>Jacob</td>
									  <td>
										<span class="badge bg-danger">Commercial</span>
									  </td>
									  <td>2019-Apr-04</td>
									  <td>
										<span class="badge bg-info">Open</span>
									  </td>
									  <td>124 548 1254</td>
									</tr>
									<tr>
									  <td>2.</td>
									  <td>William</td>
									  <td>
										<span class="badge bg-success">Residential</span>
									  </td>
									  <td>2019-Apr-15</td>
									  <td>
										<span class="badge bg-info">Open</span>
									  </td>
									  <td>845 548 1254</td>
									</tr>
									<tr>
									  <td>3.</td>
									  <td>Jayden</td>
									  <td>
										<span class="badge bg-danger">Commercial</span>
									  </td>
									  <td>2019-Apr-18</td>
									  <td>
										<span class="badge bg-info">Open</span>
									  </td>
									  <td>568 965 1254</td>
									</tr>
									<tr>
									  <td>4.</td>
									  <td>Michael</td>
									  <td>
										<span class="badge bg-danger">Commercial</span>
									  </td>
									  <td>2019-Apr-22</td>
									  <td>
										<span class="badge bg-warning">On Hold</span>
									  </td>
									  <td>965 854 1254</td>
									</tr>
									<tr>
									  <td>5.</td>
									  <td>Alexander</td>
									  <td>
										<span class="badge bg-success">Residential</span>
									  </td>
									  <td>2019-Apr-04</td>
									  <td>
										<span class="badge bg-info">Open</span>
									  </td>
									  <td>632 987 1254</td>
									</tr>
									<tr>
									  <td>6.</td>
									  <td>Anthony</td>
									  <td>
										<span class="badge bg-success">Residential</span>
									  </td>
									  <td>2019-Apr-04</td>
									  <td>
										<span class="badge bg-info">Open</span>
									  </td>
									  <td>321 456 1254</td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-5 col-lg-6 col-12">
				  <div class="box">
					<div class="box-header">
					  <h4 class="box-title">Booking Status</h4>
						<ul class="box-controls pull-right">
						  <li><a class="box-btn-close" href="#"></a></li>
						  <li><a class="box-btn-slide" href="#"></a></li>	
						  <li><a class="box-btn-fullscreen" href="#"></a></li>
						</ul>
					</div>
					<div class="box-body">
					  <div class="chart-responsive">
						<div class="chart" id="bar-chart" style="height: 354px;"></div>
					  </div>
					</div>
				  </div>
				</div>
				
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
					  <div class="box-body">
						<div class="flexbox">
						  <h5>ON GOING</h5>
						  <div class="dropdown">
							<span class="dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="ion-android-more-vertical rotate-90"></i></span>
							<div class="dropdown-menu">
							  <a class="dropdown-item" href="#"><i class="ion-android-list"></i> Details</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-add"></i> Add new</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-refresh"></i> Refresh</a>
							</div>
						  </div>
						</div>

						<div class="text-center my-2">
						  <div class="fs-60">425</div>
						  <span>ON GOING PROJECTS</span>
						</div>
					  </div>

					  <div class="card-body py-12 bg-lighter">
						<span class="text-muted me-1">Completed:</span>
						<span class="text-dark">125</span>
					  </div>

					  <div class="progress progress-xxs mt-0 mb-0">
						<div class="progress-bar bg-info" role="progressbar" style="width: 65%; height: 3px;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
					  <div class="box-body">
						<div class="flexbox">
						  <h5>COMPLATE</h5>
						  <div class="dropdown">
							<span class="dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="ion-android-more-vertical rotate-90"></i></span>
							<div class="dropdown-menu">
							  <a class="dropdown-item" href="#"><i class="ion-android-list"></i> Details</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-add"></i> Add new</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-refresh"></i> Refresh</a>
							</div>
						  </div>
						</div>

						<div class="text-center my-2">
						  <div class="fs-60">185</div>
						  <span>COMPLATE PROJECTS</span>
						</div>
					  </div>

					  <div class="box-body py-12 bg-lighter">
						<span class="text-muted me-1">Overdue Projects:</span>
						<span class="text-dark">45</span>
					  </div>

					  <div class="progress progress-xxs mt-0 mb-0">
						<div class="progress-bar bg-danger" role="progressbar" style="width: 72%; height: 3px;" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
					  <div class="box-body">
						<div class="flexbox">
						  <h5>COMMERCIAL</h5>
						  <div class="dropdown">
							<span class="dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="ion-android-more-vertical rotate-90"></i></span>
							<div class="dropdown-menu">
							  <a class="dropdown-item" href="#"><i class="ion-android-list"></i> Details</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-add"></i> Add new</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-refresh"></i> Refresh</a>
							</div>
						  </div>
						</div>

						<div class="text-center my-2">
						  <div class="fs-60">252</div>
						  <span>COMMERCIAL PROJECTS</span>
						</div>
					  </div>

					  <div class="box-body py-12 bg-lighter">
						<span class="text-muted me-1">Completed:</span>
						<span class="text-dark">176</span>
					  </div>

					  <div class="progress progress-xxs mt-0 mb-0">
						<div class="progress-bar bg-primary" role="progressbar" style="width: 55%; height: 3px;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					</div>
				</div>
				<div class="col-xl-3 col-md-6 col-12">
					<div class="box">
					  <div class="box-body">
						<div class="flexbox">
						  <h5>RESIDENTIAL</h5>
						  <div class="dropdown">
							<span class="dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="ion-android-more-vertical rotate-90"></i></span>
							<div class="dropdown-menu">
							  <a class="dropdown-item" href="#"><i class="ion-android-list"></i> Details</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-add"></i> Add new</a>
							  <a class="dropdown-item" href="#"><i class="ion-android-refresh"></i> Refresh</a>
							</div>
						  </div>
						</div>

						<div class="text-center my-2">
						  <div class="fs-60">489</div>
						  <span>RESIDENTIAL PROJECTS</span>
						</div>
					  </div>

					  <div class="box-body py-12 bg-lighter">
						<span class="text-muted me-1">Completed:</span>
						<span class="text-dark">156</span>
					  </div>

					  <div class="progress progress-xxs mt-0 mb-0">
						<div class="progress-bar bg-success" role="progressbar" style="width: 95%; height: 3px;" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
					  </div>
					</div>
				</div>
				
				<div class="col-xl-4 col-12">
					<div class="box">
						<div class="box-header">
						  <h4 class="box-title">Revenue Statistics</h4>
						</div>
						<div class="box-body chart-responsive">
						  <div class="chart" id="revenue-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-xl-8 col-12">
					<div class="box">
						<div class="box-header">
						  <h4 class="box-title">Project Sales</h4>
						</div>
						<div class="box-body">
							<div class="table-responsive">
							  <table class="table mb-0">
								<tbody>
									<tr>
									  <th>#</th>
									  <th>Project Name</th>
									  <th>Property Type</th>
									  <th>Sold Unite</th>
									  <th>Unsold Unite</th>
									  <th>Totle Unite</th>
									</tr>
									<tr>
									  <td>1.</td>
									  <td>Ipsum-zlm Tower</td>
									  <td>
										<span class="badge bg-danger">Residential</span>
									  </td>
									  <td>85</td>
									  <td>41</td>
									  <td>126</td>
									</tr>
									<tr>
									  <td>2.</td>
									  <td>Monek-pro Villa</td>
									  <td>
										<span class="badge bg-success">Residential</span>
									  </td>
									  <td>75</td>
									  <td>65</td>
									  <td>140</td>
									</tr>
									<tr>
									  <td>3.</td>
									  <td>Zila-pro Complex</td>
									  <td>
										<span class="badge bg-danger">Commercial</span>
									  </td>
									  <td>98</td>
									  <td>45</td>
									  <td>143</td>
									</tr>
									<tr>
									  <td>4.</td>
									  <td>Akil-xlm Tower</td>
									  <td>
										<span class="badge bg-danger">Residential</span>
									  </td>
									  <td>96</td>
									  <td>45</td>
									  <td>141</td>
									</tr>
									<tr>
									  <td>5.</td>
									  <td>Duler-vista Villa</td>
									  <td>
										<span class="badge bg-success">Residential</span>
									  </td>
									  <td>154</td>
									  <td>85</td>
									  <td>239</td>
									</tr>
									<tr>
									  <td>6.</td>
									  <td>Akil-xlm Tower</td>
									  <td>
										<span class="badge bg-danger">Residential</span>
									  </td>
									  <td>96</td>
									  <td>45</td>
									  <td>141</td>
									</tr>
								  </tbody>
								</table>
							</div>
						</div>
						<!-- /.box-body -->
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
$extra_js = "<script src='../assets/vendor_components/raphael/raphael.min.js'></script>
<script src='../assets/vendor_components/morris.js/morris.min.js'></script>
<script src='js/pages/reports.js'></script>";
include 'includes/footer.php';
?>
