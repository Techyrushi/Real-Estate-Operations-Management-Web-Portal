<?php
session_start();
include 'config/db.php';

$msg = "";
$error = "";
$token = $_GET['token'] ?? '';
$show_form = false;

if (empty($token)) {
    $error = "Invalid token.";
} else {
    // Validate Token
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND used = 0 AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    $reset = $stmt->fetch();
    
    if (!$reset) {
        $error = "Invalid or expired token.";
    } else {
        $show_form = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $show_form) {
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $pdo->beginTransaction();
            
            // Update User
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->execute(['password' => $hashed_password, 'email' => $reset['email']]);
            
            // Mark token as used
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = :id");
            $stmt->execute(['id' => $reset['id']]);
            
            $pdo->commit();
            $msg = "Password updated successfully! You can now login.";
            $show_form = false; // Hide form after success
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating password: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
	<!-- Vendors Style-->
	<link rel="stylesheet" href="css/vendors_css.css">
	<!-- Style-->  
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/skin_color.css">	
    <style>
        .input-group-text.cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>
<body class="hold-transition theme-primary bg-img" style="background-image: url(../images/auth-bg/bg-2.jpg)">
	
	<div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">
			<div class="col-12">
				<div class="row justify-content-center g-0">
					<div class="col-lg-5 col-md-5 col-12">
						<div class="bg-white rounded10 shadow-lg">
							<div class="content-top-agile p-20 pb-0">
								<h3 class="text-primary">Reset Password</h3>
                                <?php if ($show_form && !$msg): ?>
								<p class="mb-0">Enter your new password.</p>
                                <?php endif; ?>
							</div>
							<div class="p-40">
                                <?php if ($msg): ?>
                                    <div class="alert alert-success"><?php echo $msg; ?></div>
                                    <div class="text-center mt-20">
                                        <a href="auth_login.php" class="btn btn-primary">Go to Login</a>
                                    </div>
                                <?php elseif ($error && !$show_form): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <div class="text-center mt-20">
                                        <a href="auth_user_pass.php" class="btn btn-warning">Try Again</a>
                                    </div>
                                <?php elseif ($show_form): ?>
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
								<form action="" method="post">
									<div class="form-group">
										<div class="input-group mb-3">
											<span class="input-group-text bg-transparent"><i class="ti-lock"></i></span>
											<input type="password" name="password" id="password" class="form-control ps-15 bg-transparent" placeholder="New Password" required>
                                            <span class="input-group-text bg-transparent cursor-pointer" onclick="togglePassword('password', 'eye1')">
                                                <i class="fa fa-eye" id="eye1"></i>
                                            </span>
										</div>
									</div>
                                    <div class="form-group">
										<div class="input-group mb-3">
											<span class="input-group-text bg-transparent"><i class="ti-lock"></i></span>
											<input type="password" name="confirm_password" id="confirm_password" class="form-control ps-15 bg-transparent" placeholder="Confirm Password" required>
                                            <span class="input-group-text bg-transparent cursor-pointer" onclick="togglePassword('confirm_password', 'eye2')">
                                                <i class="fa fa-eye" id="eye2"></i>
                                            </span>
										</div>
									</div>
									  <div class="row">
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-info margin-top-10">UPDATE PASSWORD</button>
										</div>
									  </div>
								</form>	
                                <?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	

	<script src="js/vendors.min.js"></script>
    <script src="../assets/icons/feather-icons/feather.min.js"></script>	
    <script>
        function togglePassword(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>