<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h3 class="page-title">File Manager</h3>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item active" aria-current="page">File Manager</li>
							</ol>
						</nav>
					</div>
				</div>
				<div class="right-title">
					<div class="d-flex mt-10 justify-content-end">
						<a href="javascript:void(0)" class="waves-effect waves-circle btn btn-outline btn-circle btn-primary btn-sm me-5"><i class="mdi mdi-search-web fs-18"></i></a>
						<a href="javascript:void(0)" class="waves-effect waves-circle btn btn-outline btn-circle btn-primary btn-sm me-5"><i class="mdi mdi-format-list-bulleted fs-18"></i></a>
						<a href="javascript:void(0)"  data-bs-toggle="modal" data-bs-target="#modal-right" class="waves-effect waves-circle btn btn-outline btn-circle btn-primary btn-sm"><i class="mdi mdi-information fs-18"></i></a>
					</div>
				</div>
			</div>
		</div>
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-xl-3 col-lg-4 col-12">
					<div class="box">
						<div class="box-body">
						  <ul class="nav nav-pills d-block">
							<li class="nav-item">
								<a class="nav-link active rounded" href="javascript:void(0)">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/image.svg" class="img-fluid svg-icon w-20 me-10 mb-2" alt=""> 
									<span class="fs-18 mt-2">Images</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link rounded" href="javascript:void(0)">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/video-camera.svg" class="img-fluid svg-icon w-20 me-10 mb-2" alt=""> 
									<span class="fs-18 mt-2">Videos</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link rounded" href="javascript:void(0)">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/form_elements.svg" class="img-fluid svg-icon w-20 me-10 mb-2" alt=""> 
									<span class="fs-18 mt-2">Documents</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link rounded" href="javascript:void(0)">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/user.svg" class="img-fluid svg-icon w-20 me-10 mb-2" alt=""> 
									<span class="fs-18 mt-2">Shared</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link rounded" href="javascript:void(0)">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/delete.svg" class="img-fluid svg-icon w-20 me-10 mb-2" alt=""> 
									<span class="fs-18 mt-2">Trash</span>
								</a>
							</li>
						  </ul>
						  <hr>
					      <ul class="list-unstyled">
							<li>
								<a class="px-10 py-15 d-block" href="javascript:void(0)">
									<span class="badge badge-dot badge-success me-10 mb-1"></span>
									<span class="fs-18">Custom Work</span>
								</a>
							</li>
							<li>
								<a class="px-10 py-15 d-block" href="javascript:void(0)">
									<span class="badge badge-dot badge-warning me-10 mb-1"></span>
									<span class="fs-18">Important Meetings</span>
								</a>
							</li>
							<li>
								<a class="px-10 py-15 d-block" href="javascript:void(0)">
									<span class="badge badge-dot badge-primary me-10 mb-1"></span>
									<span class="fs-18">Work</span>
								</a>
							</li>
							<li>
								<a class="px-10 py-15 d-block" href="javascript:void(0)">
									<span class="badge badge-dot badge-info me-10 mb-1"></span>
									<span class="fs-18">Design</span>
								</a>
							</li>
							<li>
								<a class="px-10 py-15 d-block" href="javascript:void(0)">
									<span class="badge badge-dot badge-danger me-10 mb-1"></span>
									<span class="fs-18">Next Week</span>
								</a>
							</li>
							<li>
								<a class="px-10 py-15 d-block" href="javascript:void(0)">
									<span class="me-10 mb-1"><i class="fa fa-plus"></i></span>
									<span class="fs-18"> Add New Label </span>
								</a>
							</li>
						  </ul>
						</div>
					  </div>
				</div>
				<div class="col-xl-9 col-lg-8 col-12">
					<div class="box">
					  <div class="box-header with-border">
						  <div class="d-inline-block">
						  	<div class="lookup lookup-sm lookup-right">
							<input type="text" name="s" placeholder="Search">
						  </div>
						  </div>
						  <div class="box-controls pull-right">
							<div class="box-header-actions" style="margin-top: -10px;">
							  <button class="btn btn-primary p-0">
								  <label for="file-input-folder" class="mb-0 p-10">Upload New Files</label>
								  <input id="file-input-folder" class="d-none" type="file">								  
								</button>
							</div>
						  </div>
					  </div>
					</div>
					<div class="card-columns">
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_0" class="filled-in chk-col-success">
										<label for="md_checkbox_0" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/dashboard.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Data Folder</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_1" class="filled-in chk-col-success">
										<label for="md_checkbox_1" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/pages.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Document.text</p>
									<p class="text-fade mb-0">12 mb</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_2" class="filled-in chk-col-success">
										<label for="md_checkbox_2" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/video-camera.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Videos</p>
									<p class="text-fade mb-0">1 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_3" class="filled-in chk-col-success">
										<label for="md_checkbox_3" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/image.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Photos</p>
									<p class="text-fade mb-0">5 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_4" class="filled-in chk-col-success">
										<label for="md_checkbox_4" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="../images/avatar/1.jpg" class="img-fluid rounded w-80" alt="">
									<p class="mb-0">Photo</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_5" class="filled-in chk-col-success">
										<label for="md_checkbox_5" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/microphone.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Audio File</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_6" class="filled-in chk-col-success">
										<label for="md_checkbox_6" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/form_elements.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Document.dox</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_7" class="filled-in chk-col-success">
										<label for="md_checkbox_7" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/video-camera.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Movie</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_8" class="filled-in chk-col-success">
										<label for="md_checkbox_8" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="../images/avatar2.jpg" class="img-fluid rounded w-80" alt="">
									<p class="mb-0">Image</p>
									<p class="text-fade mb-0">15 kb</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_9" class="filled-in chk-col-success">
										<label for="md_checkbox_9" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/image.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Images</p>
									<p class="text-fade mb-0">1 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_10" class="filled-in chk-col-success">
										<label for="md_checkbox_10" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/microphone.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Audio</p>
									<p class="text-fade mb-0">1 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_11" class="filled-in chk-col-success">
										<label for="md_checkbox_11" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/dashboard.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Data Folder</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_12" class="filled-in chk-col-success">
										<label for="md_checkbox_12" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/dashboard.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Data Folder</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_13" class="filled-in chk-col-success">
										<label for="md_checkbox_13" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="../images/avatar5.jpg" class="img-fluid rounded w-80" alt="">
									<p class="mb-0">Photo</p>
									<p class="text-fade mb-0">15 KB</p>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<input type="checkbox" id="md_checkbox_14" class="filled-in chk-col-success">
										<label for="md_checkbox_14" class="mb-0"></label>
									</div>
									<div class="dropdown mb-5">
										<a data-bs-toggle="dropdown" href="#" class=""><i class="ti-more-alt"></i></a>
										<div class="dropdown-menu dropdown-menu-end">
										  <a class="dropdown-item" href="#"><i class="ti-user"></i> Share File</a>
										  <a class="dropdown-item" href="#"><i class="ti-trash"></i> Delete </a>
										</div>
									</div>
								</div>
								<div class="text-center">
									<img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/dashboard.svg" class="img-fluid svg-icon w-80" alt="">
									<p class="mb-0">Data Folder</p>
									<p class="text-fade mb-0">15 GB</p>
								</div>
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
include 'includes/footer.php';
?>
