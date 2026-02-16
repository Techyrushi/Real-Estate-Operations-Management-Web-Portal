<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_roles')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$roles = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_role_id'])) {
        $delete_id = (int)$_POST['delete_role_id'];
        $stmt = $pdo->prepare("SELECT name FROM roles WHERE id = :id");
        $stmt->execute(['id' => $delete_id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$role) {
            $error = "Role not found.";
        } elseif (strtolower($role['name']) === 'admin') {
            $error = "Admin role cannot be deleted.";
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = :id");
            $stmt->execute(['id' => $delete_id]);
            $userCount = (int)$stmt->fetchColumn();
            if ($userCount > 0) {
                $error = "Cannot delete role assigned to users.";
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = :id");
                    $stmt->execute(['id' => $delete_id]);
                    $msg = "Role deleted successfully.";
                    $roles = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
                } catch (PDOException $e) {
                    $error = "Error deleting role: " . $e->getMessage();
                }
            }
        }
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $error = "Role name is required.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO roles (name, description) VALUES (:name, :description)");
                $stmt->execute([
                    'name' => $name,
                    'description' => $description,
                ]);
                $msg = "Role created successfully.";
                $roles = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
            } catch (PDOException $e) {
                $error = "Error creating role: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Role Management</h4>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box mb-3">
                    <div class="box-header with-border">
                        <h4 class="box-title">Create New Role</h4>
                    </div>
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-success"><?php echo $msg; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Role Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Sales Manager" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" placeholder="Short description of this role">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-200" style="white-space: nowrap;"><i class="ti-plus"></i> Add Role</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Role List</h4>
                    </div>  
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Role Name</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td>#<?php echo $role['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($role['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($role['description']); ?></td>
                                        <td>
                                            <a href="admin_role_edit.php?id=<?php echo $role['id']; ?>" class="btn btn-sm btn-info">Manage Permissions</a>
                                            <?php if (strtolower($role['name']) !== 'admin'): ?>
                                                <form method="post" style="display:inline-block; margin-left:4px;" onsubmit="return confirm('Delete this role?');">
                                                    <input type="hidden" name="delete_role_id" value="<?php echo $role['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
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
