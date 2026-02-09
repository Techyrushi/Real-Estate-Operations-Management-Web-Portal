<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'config/db.php';

// Fetch event templates
$stmt = $pdo->query("SELECT * FROM event_templates ORDER BY created_at DESC");
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Calendar</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Extra</li>
								<li class="breadcrumb-item active" aria-current="page">Calendar</li>
							</ol>
						</nav>
					</div>
				</div>
				
			</div>
		</div>

		<!-- Main content -->
		<section class="content">
		  <div class="row">	
			  <div class="col-xl-9 col-lg-8 col-12">
				  <div class="box">
					  <div class="box-body">
						  <div id="calendar"></div>
					  </div>
				  </div> 
			  </div>
			  <div class="col-xl-3 col-lg-4 col-12"> 
				<div class="box no-border no-shadow">
					<div class="box-header with-border">
					  <h4 class="box-title">Draggable Events</h4>
					</div>
					<div class="box-body p-0">
					  <!-- the events -->
					  <div id="external-events">
                        <?php foreach ($templates as $tmpl): ?>
						<div class="external-event m-15 <?php echo $tmpl['class_name']; ?>" data-class="<?php echo $tmpl['class_name']; ?>"><i class="fa fa-hand-o-right"></i><?php echo htmlspecialchars($tmpl['title']); ?></div>
                        <?php endforeach; ?>
					  </div>
					  <div class="event-fc-bt mx-15 my-20">
						<!-- checkbox -->
						<!-- <div class="checkbox">
							<input id="drop-remove" type="checkbox">
							<label for="drop-remove">
								Remove after drop
							</label>
						</div> -->
						<a href="#" data-bs-toggle="modal" data-bs-target="#add-new-events" class="btn btn-success w-p100 my-10">
							<i class="ti-plus"></i> Add New Event
						</a>
					  </div>
				   </div>
                   <!-- Trash Can for Deleting Events -->
                   <div id="calendar-trash" class="box-body bg-danger text-center mt-20" style="cursor: pointer; border-radius: 5px;">
                        <i class="fa fa-trash fa-2x text-white"></i>
                        <h5 class="text-white mt-10">Drag Here to Delete</h5>
                   </div>
			    </div>
			  </div> 
			</div>
		</section>
		<!-- /.content -->
	  </div>	  
	
  </div>
  
  <!-- Modal Add Category -->
  <div class="modal fade" id="add-new-events" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		  <div class="modal-content">
			  <div class="modal-header">
				  <h4 class="modal-title" id="myModalLabel">Add a category</h4>
				  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			  <div class="modal-body">
				  <form role="form">
					  <div class="form-group">
						  <label class="form-label">Category Name</label>
						  <input type="text" class="form-control" id="category-name" name="category-name" required>
					  </div>
					  <div class="form-group">
						  <label class="form-label">Choose Category Color</label>
						  <select class="form-control" name="category-color">
							  <option value="success">Success</option>
							  <option value="danger">Danger</option>
							  <option value="info">Info</option>
							  <option value="primary">Primary</option>
							  <option value="warning">Warning</option>
							  <option value="inverse">Inverse</option>
						  </select>
					  </div>
				  </form>
			  </div>
			  <div class="modal-footer">
				  <button type="button" class="btn btn-success save-category" data-bs-dismiss="modal">Save</button>
				  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
			  </div>
		  </div>
	  </div>
  </div>
  <!-- END MODAL -->
  
  <!-- Modal Event -->
  <div class="modal fade" id="my-event" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog">
		  <div class="modal-content">
			  <div class="modal-header">
				  <h4 class="modal-title">Event</h4>
				  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			  </div>
			  <div class="modal-body"></div>
			  <div class="modal-footer">
				  <button type="button" class="btn btn-danger delete-event" data-bs-dismiss="modal">Delete</button>
				  <button type="button" class="btn btn-success save-event" data-bs-dismiss="modal">Save</button>
				  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			  </div>
		  </div>
	  </div>
  </div>
  <!-- END MODAL -->

<!-- /.content-wrapper -->
<?php
$hide_dashboard_js = true;
$extra_js = "<script src='../assets/vendor_components/jquery-ui/jquery-ui.min.js'></script>
<script src='../assets/vendor_components/perfect-scrollbar-master/perfect-scrollbar.jquery.min.js'></script>
<script src='../assets/vendor_components/fullcalendar/lib/moment.min.js'></script>
<script src='../assets/vendor_components/fullcalendar/fullcalendar.min.js'></script>
<script src='js/pages/calendar.js'></script>";
include 'includes/footer.php';
?>