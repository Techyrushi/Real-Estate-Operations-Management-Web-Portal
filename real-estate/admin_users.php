<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

// Check permissions
if (!hasRole('Admin') && !hasPermission('manage_users')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$msg = "";
$error = "";

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $delete_id = (int)$_GET['id'];
    if ($delete_id === (int)($_SESSION['user_id'] ?? 0)) {
        $error = "You cannot delete your own account.";
    } else {
        $stmt = $pdo->prepare("SELECT username, role_id FROM users WHERE id = :id");
        $stmt->execute(['id' => $delete_id]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$userRow) {
            $error = "User not found.";
        } else {
            $isAdminRole = false;
            if (!empty($userRow['role_id'])) {
                $stmtRole = $pdo->prepare("SELECT name FROM roles WHERE id = :id");
                $stmtRole->execute(['id' => $userRow['role_id']]);
                $roleRow = $stmtRole->fetch(PDO::FETCH_ASSOC);
                if ($roleRow && strtolower($roleRow['name']) === 'admin') {
                    $isAdminRole = true;
                }
            }
            if ($isAdminRole) {
                $error = "Admin user cannot be deleted.";
            } else {
                $stmtDel = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmtDel->execute(['id' => $delete_id]);
                $msg = "User deleted successfully.";
            }
        }
    }
}

// Fetch all users with roles
$stmt = $pdo->query("
    SELECT u.*, r.name as role_name 
    FROM users u 
    LEFT JOIN roles r ON u.role_id = r.id 
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">User Management</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Users</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="ms-auto">
                <a href="admin_user_create.php" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add New User</a>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-success"><?php echo $msg; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : '../images/avatar/avatar-1.png'; ?>" class="avatar avatar-sm rounded-circle me-2" alt="">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($user['username']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($user['role_name']); ?></span></td>
                                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <a href="admin_user_edit.php?id=<?php echo $user['id']; ?>" class="text-info me-2" data-bs-toggle="tooltip" title="Edit"><i class="ti-pencil"></i></a>
                                            <?php
                                            $isSelf = ($user['id'] == ($_SESSION['user_id'] ?? 0));
                                            $isAdminRole = strtolower($user['role_name'] ?? '') === 'admin';
                                            if (!$isSelf && !$isAdminRole):
                                            ?>
                                                <a href="admin_users.php?action=delete&id=<?php echo $user['id']; ?>" class="text-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Delete this user?');"><i class="ti-trash"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
