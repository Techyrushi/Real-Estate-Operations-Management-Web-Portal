<?php
session_start();
include 'config/db.php';
include 'includes/email_helper.php';

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate Token
        $token = bin2hex(random_bytes(32));
        
        // Save to DB with MySQL Time
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        $stmt->execute(['email' => $email, 'token' => $token]);
        
        // Send Email
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/auth_reset_pass.php?token=" . $token;
        
        $subject = "Password Reset Request";
        $body = "
            <h2>Password Reset Request</h2>
            <p>Hi " . htmlspecialchars($user['full_name'] ?? $user['username']) . ",</p>
            <p>We received a request to reset your password. Click the button below to set a new password:</p>
            <a href='$reset_link' class='btn'>Reset Password</a>
            <p>If you didn't request this, you can safely ignore this email.</p>
            <p>This link will expire in 1 hour.</p>
        ";
        
        if (send_email($email, $subject, $body)) {
            $msg = "A password reset link has been sent to your email address.";
        } else {
            $error = "Failed to send email. Please try again later.";
        }
    } else {
        // Security: Don't reveal if email exists or not, but for UX let's say "If that email exists..."
        // However, user usually expects feedback. Let's use generic message.
        $msg = "If an account with that email exists, we have sent a reset link.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://master-admin-template.multipurposethemes.com/bs5/images/favicon.ico">

    <title>Recover Password</title>
  
	<!-- Vendors Style-->
	<link rel="stylesheet" href="css/vendors_css.css">
	  
	<!-- Style-->  
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/skin_color.css">	

</head>
<body class="hold-transition theme-primary bg-img" style="background-image: url(../images/auth-bg/bg-2.jpg)">
	
	<div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">
			
			<div class="col-12">
				<div class="row justify-content-center g-0">
					<div class="col-lg-5 col-md-5 col-12">
						<div class="bg-white rounded10 shadow-lg">
							<div class="content-top-agile p-20 pb-0">
								<h3 class="text-primary">Recover Password</h3>
								<p class="mb-0">Enter your email to reset your password.</p>							
							</div>
							<div class="p-40">
								<form action="" method="post">
                                    <?php if ($msg): ?>
                                        <div class="alert alert-success"><?php echo $msg; ?></div>
                                    <?php endif; ?>
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
									<div class="form-group">
										<div class="input-group mb-3">
											<span class="input-group-text bg-transparent"><i class="ti-email"></i></span>
											<input type="email" name="email" class="form-control ps-15 bg-transparent" placeholder="Your Email" required>
										</div>
									</div>
									  <div class="row">
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-info margin-top-10">RESET</button>
										</div>
										<!-- /.col -->
									  </div>
								</form>	
							</div>
                            <div class="text-center pb-20">
                                <p class="mb-0">Return to <a href="auth_login.php" class="text-warning">Sign In</a></p>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	

	<!-- Vendor JS -->
	<script src="js/vendors.min.js"></script>
    <script src="../assets/icons/feather-icons/feather.min.js"></script>	

</body>
</html>