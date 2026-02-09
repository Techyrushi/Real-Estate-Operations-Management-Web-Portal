<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_users')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: admin_users.php");
    exit();
}

$msg = "";
$error = "";

// Fetch User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$edit_user = $stmt->fetch();

if (!$edit_user) {
    echo "User not found";
    exit();
}

// Fetch Roles
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role_id = $_POST['role_id'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $password = $_POST['password'] ?? '';

    $password_sql = "";
    $params = [
        'username' => $username,
        'email' => $email,
        'role' => $role_id,
        'name' => $full_name,
        'id' => $user_id
    ];

    if (!empty($password)) {
        $password_sql = ", password = :password";
        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql = "UPDATE users SET username = :username, email = :email, role_id = :role, full_name = :name $password_sql WHERE id = :id";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $msg = "User updated successfully!";
        // Refresh
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $edit_user = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error updating user: " . $e->getMessage();
    }
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Edit User</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="admin_users.php">Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Edit User Details</h4>
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
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = 'admin_users.php';
                                        }
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

                        <form method="post" onsubmit="Swal.fire({title: 'Processing...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($edit_user['full_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($edit_user['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['id']; ?>" <?php echo ($role['id'] == $edit_user['role_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="box-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti-save"></i> Update User
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