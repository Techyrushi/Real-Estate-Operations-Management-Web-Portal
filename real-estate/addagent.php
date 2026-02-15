<?php
include 'includes/header.php';
include 'includes/sidebar.php';

$id = $_GET['id'] ?? null;
$success_msg = '';
$agent = [
	'first_name' => '',
	'last_name' => '',
	'phone' => '',
	'email' => '',
	'dob' => '',
	'age' => '',
	'gender' => '',
	'description' => '',
	'facebook' => '',
	'twitter' => '',
	'instagram' => '',
	'photo' => '',
	'status' => 'Active'
];
$error = '';

if ($id) {
	$stmt = $pdo->prepare("SELECT * FROM agents WHERE id = ?");
	$stmt->execute([$id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($row) {
		$agent['first_name'] = $row['first_name'] ?? '';
		$agent['last_name'] = $row['last_name'] ?? '';
		$agent['phone'] = $row['phone'] ?? '';
		$agent['email'] = $row['email'] ?? '';
		$agent['dob'] = $row['dob'] ?? '';
		$agent['age'] = $row['age'] ?? '';
		$agent['gender'] = $row['gender'] ?? '';
		$agent['description'] = $row['description'] ?? '';
		$agent['facebook'] = $row['facebook'] ?? '';
		$agent['twitter'] = $row['twitter'] ?? '';
		$agent['instagram'] = $row['instagram'] ?? '';
		$agent['photo'] = $row['photo'] ?? '';
		$agent['status'] = $row['status'] ?? 'Active';
	} else {
		$error = 'Agent not found.';
		$id = null;
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id = $_POST['id'] ?? $id;
	$agent['first_name'] = trim($_POST['first_name'] ?? '');
	$agent['last_name'] = trim($_POST['last_name'] ?? '');
	$agent['phone'] = trim($_POST['phone'] ?? '');
	$agent['email'] = trim($_POST['email'] ?? '');
	$agent['dob'] = $_POST['dob'] ?? '';
	$agent['age'] = $_POST['age'] !== '' ? (int) $_POST['age'] : null;
	$agent['gender'] = $_POST['gender'] ?? '';
	$agent['description'] = trim($_POST['description'] ?? '');
	$agent['facebook'] = trim($_POST['facebook'] ?? '');
	$agent['twitter'] = trim($_POST['twitter'] ?? '');
	$agent['instagram'] = trim($_POST['instagram'] ?? '');
	$agent['status'] = $_POST['status'] ?? 'Active';

	if ($agent['first_name'] === '') {
		$error = 'First name is required.';
	} elseif ($agent['phone'] === '' && $agent['email'] === '') {
		$error = 'Phone or email is required.';
	}

	if ($error === '') {
		$photoPath = $agent['photo'];
		if (!empty($_FILES['photo']['name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
			$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
			if (in_array(mime_content_type($_FILES['photo']['tmp_name']), $allowed_types, true)) {
				$ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
				$ext = strtolower($ext);
				if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
					$upload_root = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'agents' . DIRECTORY_SEPARATOR;
					if (!is_dir($upload_root)) {
						mkdir($upload_root, 0777, true);
					}
					$filename = 'agent_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
					$target = $upload_root . $filename;
					if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
						$photoPath = 'uploads/agents/' . $filename;
					}
				}
			}
		}
		$agent['photo'] = $photoPath;
		$gender = in_array($agent['gender'], ['Male', 'Female', 'Other']) ? $agent['gender'] : 'Male';
		$status = $agent['status'] === 'Inactive' ? 'Inactive' : 'Active';
		if ($id) {
			$stmt = $pdo->prepare("UPDATE agents SET first_name = ?, last_name = ?, phone = ?, email = ?, dob = ?, age = ?, gender = ?, description = ?, facebook = ?, twitter = ?, instagram = ?, photo = ?, status = ? WHERE id = ?");
			$stmt->execute([
				$agent['first_name'],
				$agent['last_name'],
				$agent['phone'],
				$agent['email'],
				$agent['dob'] ?: null,
				$agent['age'],
				$gender,
				$agent['description'],
				$agent['facebook'],
				$agent['twitter'],
				$agent['instagram'],
				$agent['photo'],
				$status,
				$id
			]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO agents (first_name, last_name, phone, email, dob, age, gender, description, facebook, twitter, instagram, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->execute([
				$agent['first_name'],
				$agent['last_name'],
				$agent['phone'],
				$agent['email'],
				$agent['dob'] ?: null,
				$agent['age'],
				$gender,
				$agent['description'],
				$agent['facebook'],
				$agent['twitter'],
				$agent['instagram'],
				$agent['photo'],
				$status
			]);
			$id = $pdo->lastInsertId();
		}

		$success_msg = $id ? "Agent updated successfully." : "Agent added successfully.";
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="container-full">
		<!-- Main content -->
		<section class="content">
            <?php if ($success_msg): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: <?php echo json_encode($success_msg); ?>,
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'agentslist.php';
                    });
                });
            </script>
            <?php endif; ?>
			<?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
			<form method="POST" enctype="multipart/form-data"
				action="addagent.php<?php echo $id ? '?id=' . (int) $id : ''; ?>"
				onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
				<input type="hidden" name="id" value="<?php echo $id ? (int) $id : ''; ?>">
				<div class="row">
					<div class="col-12">
						<div class="box">
							<div class="box-header">
								<h4 class="box-title">Basic Information</h4>
								<ul class="box-controls pull-right">
									<li class="dropdown">
										<a data-bs-toggle="dropdown" href="#" class="px-10 hover-primary"><i
												class="ti-menu hover-primary"></i></a>
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
											<input type="text" class="form-control" name="first_name"
												placeholder="First Name"
												value="<?php echo htmlspecialchars($agent['first_name']); ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<input type="text" class="form-control" name="last_name"
												placeholder="Last Name"
												value="<?php echo htmlspecialchars($agent['last_name']); ?>">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<input type="text" class="form-control" name="phone" placeholder="Phone No."
												value="<?php echo htmlspecialchars($agent['phone']); ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<input type="date" class="form-control" name="dob"
												placeholder="Date of Birth"
												value="<?php echo htmlspecialchars($agent['dob']); ?>">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<input type="number" class="form-control" name="age" placeholder="Age"
												value="<?php echo htmlspecialchars($agent['age']); ?>">
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<select class="form-select select2" name="gender">
												<option value="">Select Gender</option>
												<option value="Male" <?php echo $agent['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
												<option value="Female" <?php echo $agent['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
												<option value="Other" <?php echo $agent['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
											</select>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<input type="email" class="form-control" name="email"
												placeholder="Enter Your Email"
												value="<?php echo htmlspecialchars($agent['email']); ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<select class="form-select" name="status">
												<option value="Active" <?php echo $agent['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
												<option value="Inactive" <?php echo $agent['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
											</select>
										</div>
									</div>
								
									<div class="col-sm-4">
										<div class="form-group">
											<?php if (!empty($agent['photo'])): ?>
												<div class="mb-10">
													<img src="<?php echo '../' . htmlspecialchars($agent['photo']); ?>"
														alt="Agent Photo" class="img-thumbnail" width="120">
												</div>
											<?php endif; ?>
											<input type="file" class="form-control" name="photo" accept="image/*">
										</div>
									</div>
									<div class="col-12 mt-10">
										<div class="form-group mb-0">
											<textarea rows="4" class="form-control no-resize" name="description"
												placeholder="Description"><?php echo htmlspecialchars($agent['description']); ?></textarea>
										</div>
									</div>
								</div>
							</div>

							<div class="col-lg-12 col-12">
								<div class="box">
									<div class="box-header">
										<h4 class="box-title">Social Information</h4>
										<ul class="box-controls pull-right">
											<li class="dropdown">
												<a data-bs-toggle="dropdown" href="#" class="px-10 hover-primary"><i
														class="ti-menu hover-primary"></i></a>
												<div class="dropdown-menu">
													<a class="dropdown-item" href="#"><i class="ti-import"></i>
														Import</a>
													<a class="dropdown-item" href="#"><i class="ti-export"></i>
														Export</a>
													<a class="dropdown-item" href="#"><i class="ti-printer"></i>
														Print</a>
													<div class="dropdown-divider"></div>
													<a class="dropdown-item" href="#"><i class="ti-settings"></i>
														Settings</a>
												</div>
											</li>
										</ul>
									</div>
									<div class="box-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<input type="text" class="form-control" name="facebook"
														placeholder="Facebook"
														value="<?php echo htmlspecialchars($agent['facebook']); ?>">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<input type="text" class="form-control" name="twitter"
														placeholder="Twitter"
														value="<?php echo htmlspecialchars($agent['twitter']); ?>">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group mb-md-0">
													<input type="text" class="form-control" name="instagram"
														placeholder="Instagram"
														value="<?php echo htmlspecialchars($agent['instagram']); ?>">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<a href="agentslist.php" class="btn btn-danger mr-1 waves-effect waves-light">
									<i class="ti-trash"></i> Cancel
								</a>
								<button type="submit" class="btn btn-primary waves-effect waves-light">
									<i class="ti-save-alt"></i> Submit
								</button>
							</div>
						</div>
					</div>

				</div>
			</form>
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
