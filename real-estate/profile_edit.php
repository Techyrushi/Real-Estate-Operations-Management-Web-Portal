<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: auth_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $mobile_number = $_POST['mobile_number'] ?? '';
    $about = $_POST['about'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $twitter = $_POST['twitter'] ?? '';
    $instagram = $_POST['instagram'] ?? '';
    
    $social_links = json_encode([
        'facebook' => $facebook,
        'twitter' => $twitter,
        'instagram' => $instagram,
        'about' => $about
    ]);
    
    // Image Upload
    $profile_image = $_POST['current_image'] ?? '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "../images/avatar/"; 
        // Create directory if not exists (though typically exists)
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $filename = "user_" . $user_id . "_" . time() . "." . pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $target_file = $target_dir . $filename;
        
        // Check file type/size in a real app
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        }
    }

    // Password Update
    $password_sql = "";
    $params = [
        'full_name' => $full_name,
        'mobile' => $mobile_number,
        'image' => $profile_image,
        'social' => $social_links,
        'id' => $user_id
    ];

    if (!empty($_POST['password'])) {
        $password_sql = ", password = :password";
        $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $sql = "UPDATE users SET full_name = :full_name, mobile_number = :mobile, profile_image = :image, social_media_links = :social $password_sql WHERE id = :id";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $msg = "Profile updated successfully!";
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();
        
        // Update session
        $_SESSION['profile_image'] = $user['profile_image'];
        $_SESSION['username'] = $user['username']; // In case we allow username change later, but currently read-only
    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
} else {
    // Fetch current user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();
}

$social = json_decode($user['social_media_links'] ?? '{}', true);
?>

<div class="content-wrapper">
  <div class="container-full">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Edit Profile</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Personal Details</h4>
                    </div>
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: '<?php echo $msg; ?>',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            </script>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: '<?php echo $error; ?>'
                                    });
                                });
                            </script>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">About Me</label>
                                        <textarea class="form-control" name="about" rows="3"><?php echo htmlspecialchars($social['about'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email (Read Only)</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Username (Read Only)</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Profile Image</label>
                                        <input type="file" class="form-control" name="profile_image">
                                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($user['profile_image'] ?? ''); ?>">
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Current Profile" width="100" class="rounded">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">New Password (Leave blank to keep current)</label>
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title mt-20">Social Media Links</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Facebook</label>
                                        <input type="text" class="form-control" name="facebook" value="<?php echo htmlspecialchars($social['facebook'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Twitter (X)</label>
                                        <input type="text" class="form-control" name="twitter" value="<?php echo htmlspecialchars($social['twitter'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Instagram</label>
                                        <input type="text" class="form-control" name="instagram" value="<?php echo htmlspecialchars($social['instagram'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti-save-alt"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>