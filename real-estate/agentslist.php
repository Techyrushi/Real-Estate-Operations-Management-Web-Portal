<?php
include 'includes/header.php';
include 'includes/sidebar.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['follow_agent_id']) && isset($_SESSION['user_id'])) {
    $agent_id = (int)$_POST['follow_agent_id'];
    if ($agent_id > 0) {
        $user_id = (int)$_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT id FROM user_follows WHERE user_id = ? AND entity_type = 'agent' AND entity_id = ?");
        $stmt->execute([$user_id, $agent_id]);
        if ($stmt->fetchColumn()) {
            $del = $pdo->prepare("DELETE FROM user_follows WHERE user_id = ? AND entity_type = 'agent' AND entity_id = ?");
            $del->execute([$user_id, $agent_id]);
        } else {
            $ins = $pdo->prepare("INSERT IGNORE INTO user_follows (user_id, entity_type, entity_id) VALUES (?, 'agent', ?)");
            $ins->execute([$user_id, $agent_id]);
        }
    }
    $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'agentslist.php';
    header("Location: " . $redirect);
    exit();
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM agents WHERE id = ?");
        $stmt->execute([$delete_id]);
    }
    $msg = "Agent deleted successfully";
}

if (!$msg && isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM agents WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($status_filter !== '') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_agents = count($agents);
$active_agents = 0;
$inactive_agents = 0;
foreach ($agents as $a) {
    $status_val = $a['status'] ?? 'Active';
    if ($status_val === 'Active') {
        $active_agents++;
    } else {
        $inactive_agents++;
    }
}

$followed_agents = [];
if (!empty($agents) && isset($_SESSION['user_id'])) {
    $agent_ids = array_column($agents, 'id');
    $placeholders = implode(',', array_fill(0, count($agent_ids), '?'));
    $sql_follow = "SELECT entity_id FROM user_follows WHERE user_id = ? AND entity_type = 'agent'";
    $params_follow = [(int)$_SESSION['user_id']];
    if ($placeholders) {
        $sql_follow .= " AND entity_id IN ($placeholders)";
        $params_follow = array_merge($params_follow, $agent_ids);
    }
    $stmt_follow = $pdo->prepare($sql_follow);
    $stmt_follow->execute($params_follow);
    $followed_agents = $stmt_follow->fetchAll(PDO::FETCH_COLUMN);
}
?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-primary-light">
                                <div class="box-body">
                                    <h4 class="mb-0"><?php echo $total_agents; ?></h4>
                                    <span class="text-muted">Total Agents</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-success-light">
                                <div class="box-body">
                                    <h4 class="mb-0"><?php echo $active_agents; ?></h4>
                                    <span class="text-muted">Active</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-danger-light">
                                <div class="box-body">
                                    <h4 class="mb-0"><?php echo $inactive_agents; ?></h4>
                                    <span class="text-muted">Inactive</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="box bg-info-light">
                                <div class="box-body">
                                    <h4 class="mb-0"><?php echo $total_agents > 0 ? round(($active_agents / max($total_agents, 1)) * 100) : 0; ?>%</h4>
                                    <span class="text-muted">Active %</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($msg): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: <?php echo json_encode($msg); ?>,
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'agentslist.php';
                    });
                });
            </script>
            <?php endif; ?>
            <div class="row mb-3">
                <div class="col-md-7">
                    <form method="GET" action="agentslist.php" class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, phone, email" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="Active" <?php echo $status_filter === 'Active' ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo $status_filter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex">
                            <button type="submit" class="btn btn-primary me-2"><i class="ti-search"></i></button>
                            <a href="agentslist.php" class="btn btn-secondary"><i class="ti-reload"></i></a>
                        </div>
                    </form>
                </div>
                <div class="col-md-5 text-end">
                    <a href="addagent.php" class="btn btn-success"><i class="ti-plus"></i> Add Agent</a>
                </div>
            </div>
			<div class="row">
                <?php if (!empty($agents)): ?>
                    <?php foreach ($agents as $agent): ?>
				  <div class="col-12 col-lg-4">
					<div class="box">
					  <div class="box-header no-border p-0">				
						<div class="position-relative">
                          <?php
                          $photo_src = '../images/avatar/375x200/4.jpg';
                          if (!empty($agent['photo'])) {
                              $photo_src = '../' . htmlspecialchars($agent['photo']);
                          }
                          ?>
						  <img class="img-fluid" src="<?php echo $photo_src; ?>" alt="">
                          <span class="badge badge-<?php echo ($agent['status'] ?? 'Active') === 'Active' ? 'success' : 'danger'; ?> position-absolute top-0 end-0 m-10">
                              <?php echo htmlspecialchars($agent['status'] ?? 'Active'); ?>
                          </span>
						</div>
					  </div>
					  <div class="box-body">
						  <div class="text-center">
                            <div class="user-contact list-inline text-center">
								<?php if (!empty($agent['facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($agent['facebook']); ?>" target="_blank" class="btn btn-circle mb-5 btn-facebook"><i class="fa fa-facebook"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($agent['instagram'])): ?>
								<a href="<?php echo htmlspecialchars($agent['instagram']); ?>" target="_blank" class="btn btn-circle mb-5 btn-instagram"><i class="fa fa-instagram"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($agent['twitter'])): ?>
								<a href="<?php echo htmlspecialchars($agent['twitter']); ?>" target="_blank" class="btn btn-circle mb-5 btn-twitter"><i class="fa-brands fa-x-twitter"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($agent['email'])): ?>
								<a href="mailto:<?php echo htmlspecialchars($agent['email']); ?>" class="btn btn-circle mb-5 btn-warning"><i class="fa fa-envelope"></i></a>
                                <?php endif; ?>
							</div>
							<h3 class="my-10">
                                <a href="agentprofile.php?id=<?php echo (int)$agent['id']; ?>">
                                    <?php echo htmlspecialchars(trim(($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? '')) ?: 'Agent'); ?>
                                </a>
                            </h3>
							<h6 class="user-info mt-0 mb-10">
                                <i class="fa fa-phone me-5"></i><?php echo htmlspecialchars($agent['phone'] ?? ''); ?>
                            </h6>
							<p class="w-p85 mx-auto">
                                <i class="fa fa-envelope me-5"></i> <?php echo htmlspecialchars($agent['email'] ?? ''); ?>
                            </p>
                            <div class="mt-10">
                                <form method="POST" action="agentslist.php" class="d-inline">
                                    <input type="hidden" name="follow_agent_id" value="<?php echo (int)$agent['id']; ?>">
                                    <?php
                                    $is_followed = in_array($agent['id'], $followed_agents);
                                    $follow_class = $is_followed ? 'btn-success' : 'btn-outline-success';
                                    $follow_title = $is_followed ? 'Unfollow' : 'Follow';
                                    ?>
                                    <button type="submit" class="btn btn-sm <?php echo $follow_class; ?> me-2" title="<?php echo $follow_title; ?>">
                                        <i class="ti-heart"></i>
                                    </button>
                                </form>
                                <a href="addagent.php?id=<?php echo (int)$agent['id']; ?>" class="btn btn-sm btn-info me-2"><i class="ti-pencil"></i> Edit</a>
                                <a href="#" class="btn btn-sm btn-danger" onclick="return confirmDeleteAgent(<?php echo (int)$agent['id']; ?>);"><i class="ti-trash"></i> Delete</a>
                            </div>
						  </div>
					  </div>
					</div>
				  </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body text-center">
                                <h4>No agents found.</h4>
                                <a href="addagent.php" class="btn btn-primary mt-10"><i class="ti-plus"></i> Add Agent</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
			</div>
		</section>
		<!-- /.content -->
	  </div>
  </div>
  <!-- /.content-wrapper -->
  <script>
    function confirmDeleteAgent(id) {
        if (!id) return false;
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the agent record.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deleting agent...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                window.location.href = 'agentslist.php?delete_id=' + id;
            }
        });
        return false;
    }
  </script>
  <?php
  $hide_dashboard_js = true;
  include 'includes/footer.php';
  ?>
