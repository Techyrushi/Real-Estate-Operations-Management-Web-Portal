<?php
include 'includes/header.php';
include 'includes/sidebar.php';

$agent_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$agent = null;

if ($agent_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM agents WHERE id = ?");
    $stmt->execute([$agent_id]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$agent) {
    header("Location: agentslist.php?msg=" . urlencode("Agent not found"));
    exit;
}

$full_name = trim(($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? ''));
if ($full_name === '') {
    $full_name = 'Agent';
}

$photo_src = '../images/avatar/1.jpg';
if (!empty($agent['photo'])) {
    $photo_src = '../' . $agent['photo'];
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Main content -->
		<section class="content">			
			<div class="row">
				<div class="col-12 col-lg-5 col-xl-4">
				  <div class="box">
						<div class="box-body">
							<div class="d-flex flex-row">
								<div class=""><img src="<?php echo htmlspecialchars($photo_src); ?>" alt="user" class="rounded-circle" width="100"></div>
								<div class="ps-20">
									<h3><?php echo htmlspecialchars($full_name); ?></h3>
									<h6><?php echo htmlspecialchars($agent['email'] ?? ''); ?></h6>
									<?php if (!empty($agent['phone'])): ?>
										<p class="mb-0"><i class="fa fa-phone me-5"></i><?php echo htmlspecialchars($agent['phone']); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="box-body">
							<?php if (!empty($agent['description'])): ?>
								<p class="text-center">
									<?php echo nl2br(htmlspecialchars($agent['description'])); ?>
								</p>
							<?php endif; ?>
							<ul class="list-inline text-center">
								<?php if (!empty($agent['instagram'])): ?>
									<li><a href="<?php echo htmlspecialchars($agent['instagram']); ?>" target="_blank"><i class="fa fa-instagram fs-20"></i></a></li>
								<?php endif; ?>
								<?php if (!empty($agent['twitter'])): ?>
									<li><a href="<?php echo htmlspecialchars($agent['twitter']); ?>" target="_blank"><i class="fa-brands fa-x-twitter fs-20"></i></a></li>
								<?php endif; ?>
								<?php if (!empty($agent['facebook'])): ?>
									<li><a href="<?php echo htmlspecialchars($agent['facebook']); ?>" target="_blank"><i class="fa fa-facebook-square fs-20"></i></a></li>
								<?php endif; ?>
							</ul>
						</div>
					</div>		
				  <div class="box">
					<div class="box-body box-profile">            
					  <div class="row">
						<div class="col-12">
							<div>
								<p>Email :<span class="text-gray ps-10"><?php echo htmlspecialchars($agent['email'] ?? ''); ?></span> </p>
								<p>Phone :<span class="text-gray ps-10"><?php echo htmlspecialchars($agent['phone'] ?? ''); ?></span></p>
								<p>Status :<span class="text-gray ps-10"><?php echo htmlspecialchars($agent['status'] ?? 'Active'); ?></span></p>
							</div>
						</div>
						<div class="col-12">
							<div class="pb-15">						
								<p class="mb-10">Social Profile</p>
								<div class="user-social-acount">
									<?php if (!empty($agent['facebook'])): ?>
										<a href="<?php echo htmlspecialchars($agent['facebook']); ?>" target="_blank" class="btn btn-circle btn-social-icon btn-facebook"><i class="fa fa-facebook"></i></a>
									<?php endif; ?>
									<?php if (!empty($agent['twitter'])): ?>
										<a href="<?php echo htmlspecialchars($agent['twitter']); ?>" target="_blank" class="btn btn-circle btn-social-icon btn-twitter"><i class="fa-brands fa-x-twitter"></i></a>
									<?php endif; ?>
									<?php if (!empty($agent['instagram'])): ?>
										<a href="<?php echo htmlspecialchars($agent['instagram']); ?>" target="_blank" class="btn btn-circle btn-social-icon btn-instagram"><i class="fa fa-instagram"></i></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					  </div>
					</div>
				  </div>				  
			  </div>
				<div class="col-12 col-lg-7 col-xl-8">
				
			  <div class="box">
				  <div class="box-header with-border">
					  <h4 class="box-title">Agent Details</h4>
				  </div>
				  <div class="box-body">
					  <div class="row">
						  <div class="col-md-6">
							  <p><strong>Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
							  <p><strong>Email:</strong> <?php echo htmlspecialchars($agent['email'] ?? ''); ?></p>
							  <p><strong>Phone:</strong> <?php echo htmlspecialchars($agent['phone'] ?? ''); ?></p>
						  </div>
						  <div class="col-md-6">
							  <p><strong>Gender:</strong> <?php echo htmlspecialchars($agent['gender'] ?? ''); ?></p>
							  <p><strong>Date of Birth:</strong> <?php echo $agent['dob'] ? date('d-M-Y', strtotime($agent['dob'])) : ''; ?></p>
							  <p><strong>Age:</strong> <?php echo htmlspecialchars((string)($agent['age'] ?? '')); ?></p>
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


