<?php
session_start();
include 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id']; // Store role_id
        $_SESSION['last_activity'] = time();

        // Fetch role name
        $roleStmt = $pdo->prepare("SELECT name FROM roles WHERE id = :id");
        $roleStmt->execute(['id' => $user['role_id']]);
        $_SESSION['role_name'] = $roleStmt->fetchColumn();
        
        // Store profile image
        $_SESSION['profile_image'] = $user['profile_image'];

        // Fetch permissions
        $permStmt = $pdo->prepare("
            SELECT p.slug 
            FROM permissions p 
            JOIN role_permissions rp ON p.id = rp.permission_id 
            WHERE rp.role_id = :role_id
        ");
        $permStmt->execute(['role_id' => $user['role_id']]);
        $_SESSION['permissions'] = $permStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Log login action
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details) VALUES (:user_id, 'LOGIN', 'User logged in')");
        $logStmt->execute(['user_id' => $user['id']]);

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from master-admin-template.multipurposethemes.com/bs5/real-estate/auth_login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Feb 2026 09:56:02 GMT -->
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://master-admin-template.multipurposethemes.com/bs5/images/favicon.ico">

    <title>Master Admin - Log in </title>
  
	<!-- Vendors Style-->
	<link rel="stylesheet" href="css/vendors_css.css">

	  
	<!-- Style-->  
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/skin_color.css">	

</head>
	
<body class="hold-transition theme-primary bg-img" style="background-image: url(../images/auth-bg/bg-1.jpg)">
	
	<div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">	
			
			<div class="col-12">
				<div class="row justify-content-center g-0">
					<div class="col-lg-5 col-md-5 col-12">
						<div class="bg-white rounded10 shadow-lg">
							<div class="content-top-agile p-20 pb-0">
								<h2 class="text-primary">Let's Get Started</h2>
								<p class="mb-0">Sign in to continue to WebkitX.</p>							
							</div>
							<div class="p-40">
								<form action="" method="post">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
									<div class="form-group">
										<div class="input-group mb-3">
											<span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
											<input type="text" name="username" class="form-control ps-15 bg-transparent" placeholder="Username" required>
										</div>
									</div>
									<div class="form-group">
										<div class="input-group mb-3">
											<span class="input-group-text  bg-transparent"><i class="ti-lock"></i></span>
											<input type="password" name="password" class="form-control ps-15 bg-transparent" placeholder="Password" required>
										</div>
									</div>
									  <div class="row">
										<div class="col-6">
										  <div class="checkbox">
											<input type="checkbox" id="basic_checkbox_1" >
											<label for="basic_checkbox_1">Remember Me</label>
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-6">
										 <div class="fog-pwd text-end">
											<a href="auth_user_pass.php" class="hover-warning"><i class="ion ion-locked"></i> Forgot pwd?</a><br>
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-danger mt-10">SIGN IN</button>
										</div>
										<!-- /.col -->
									  </div>
								</form>	
								<!-- Sign up link removed as per requirement -->
							</div>						
						</div>
						<!-- <div class="text-center">
						  <p class="mt-20 text-white">- Sign With -</p>
						  <p class="gap-items-2 mb-20">
							  <a class="btn btn-social-icon btn-round btn-facebook" href="#"><i class="fa fa-facebook"></i></a>
							  <a class="btn btn-social-icon btn-round btn-twitter" href="#"><i class="fa-brands fa-x-twitter"></i></a>
							  <a class="btn btn-social-icon btn-round btn-instagram" href="#"><i class="fa fa-instagram"></i></a>
							</p>	
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Vendor JS -->
	<script src="js/vendors.min.js"></script>
	<script src="js/pages/chat-popup.js"></script>
    <script src="../assets/icons/feather-icons/feather.min.js"></script>	

</body>

<!-- Mirrored from master-admin-template.multipurposethemes.com/bs5/real-estate/auth_login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Feb 2026 09:56:02 GMT -->
</html>


