<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'config/db.php';

// Fetch columns
$stmt = $pdo->query("SELECT * FROM taskboard_columns ORDER BY position ASC");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch tasks
$stmt = $pdo->query("SELECT t.*, u.username as assigned_to_name FROM tasks t LEFT JOIN users u ON t.user_id = u.id ORDER BY position ASC, created_at DESC");
$all_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group tasks
$tasks_by_column = [];
foreach ($columns as $col) {
    $tasks_by_column[$col['slug']] = [];
}
foreach ($all_tasks as $task) {
    if (isset($tasks_by_column[$task['status']])) {
        $tasks_by_column[$task['status']][] = $task;
    }
}
?>
<div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Project Taskboard</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Extra</li>
								<li class="breadcrumb-item active" aria-current="page">Taskboard</li>
							</ol>
						</nav>
					</div>
				</div>
                <div class="d-inline-block">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-task-modal">
                        <i class="ti-plus"></i> Add New Task
                    </button>
                </div>
			</div>
		</div>  

		<!-- Main content -->
		<section class="content">
		  <div class="row">
            <?php foreach ($columns as $col): ?>
			<div class="col-xl-4 col-12">
			  <div class="box">
				<div class="box-header with-border">
				  <h4 class="box-title"><?php echo htmlspecialchars($col['name']); ?></h4>
				</div>
				<div class="box-body p-0">
				  <ul class="todo-list connectedSortable" data-status="<?php echo $col['slug']; ?>">
                    <?php if (!empty($tasks_by_column[$col['slug']])): ?>
                        <?php foreach ($tasks_by_column[$col['slug']] as $task): 
                            $badge_color = 'bg-primary';
                            if ($task['priority'] == 'high') $badge_color = 'bg-warning';
                            if ($task['priority'] == 'urgent') $badge_color = 'bg-danger';
                            if ($task['priority'] == 'low') $badge_color = 'bg-info';
                        ?>
                        <li class="p-15" data-id="<?php echo $task['id']; ?>">
                        <div class="box p-15 mb-0 d-block bb-2 border-<?php echo str_replace('bg-', '', $badge_color); ?>">
                            <!-- drag handle -->
                            <span class="handle">
                                <i class="fa fa-ellipsis-v"></i>
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <!-- checkbox -->
                            <input type="checkbox" id="task_<?php echo $task['id']; ?>" class="filled-in">
                            <label for="task_<?php echo $task['id']; ?>" class="mb-0 h-15 ms-15"></label>
                            
                            <span class="pull-right badge <?php echo $badge_color; ?>"><?php echo ucfirst($task['priority']); ?></span>
                            <span class="fs-18 text-line"><?php echo htmlspecialchars($task['title']); ?></span>
                            
                            <ul class="list-inline mb-0 mt-15 ms-30">
                                <li>
                                    <span class="text-muted"><i class="fa fa-clock-o"></i> <?php echo date('M d', strtotime($task['created_at'])); ?></span>
                                </li>
                                <?php if ($task['assigned_to_name']): ?>
                                <li>
                                    <a href="#" data-bs-toggle="tooltip" title="Assigned to: <?php echo htmlspecialchars($task['assigned_to_name']); ?>">
                                        <i class="mdi mdi-account"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="pull-right">
                                    <a href="#" class="text-danger delete-task" data-id="<?php echo $task['id']; ?>" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
				  </ul>
				</div>
			  </div>
			</div>
            <?php endforeach; ?>
		  </div>
		</section>
		<!-- /.content -->
	  </div>	  
	
  </div>

<!-- Add Task Modal -->
<div class="modal fade" id="add-task-modal" tabindex="-1" role="dialog" aria-labelledby="addTaskLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTaskLabel">Add New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add-task-form">
          <div class="form-group">
            <label for="task-title" class="col-form-label">Title:</label>
            <input type="text" class="form-control" id="task-title" name="title" required>
          </div>
          <div class="form-group">
            <label for="task-desc" class="col-form-label">Description:</label>
            <textarea class="form-control" id="task-desc" name="description"></textarea>
          </div>
          <div class="form-group">
            <label for="task-status" class="col-form-label">Status:</label>
            <select class="form-control" id="task-status" name="status">
                <?php foreach ($columns as $col): ?>
                    <option value="<?php echo $col['slug']; ?>"><?php echo htmlspecialchars($col['name']); ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="task-priority" class="col-form-label">Priority:</label>
            <select class="form-control" id="task-priority" name="priority">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save-task-btn">Save Task</button>
      </div>
    </div>
  </div>
</div>
  
<!-- /.content-wrapper -->
<?php
$extra_js = "<script src='../assets/vendor_components/jquery-ui/jquery-ui.min.js'></script>
<script src='js/pages/extra_taskboard.js'></script>";
include 'includes/footer.php';
?>