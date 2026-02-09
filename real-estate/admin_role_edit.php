<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 

if (!hasRole('Admin') && !hasPermission('manage_roles')) {
    echo "<div class='content-wrapper'><div class='container-full'><section class='content'><div class='alert alert-danger'>Access Denied</div></section></div></div>";
    include 'includes/footer.php';
    exit();
}

$role_id = $_GET['id'] ?? null;
if (!$role_id) {
    header("Location: admin_roles.php");
    exit();
}

// Fetch Role
$stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
$stmt->execute(['id' => $role_id]);
$role = $stmt->fetch();

if (!$role) {
    echo "Role not found";
    exit();
}

// Fetch All Permissions
$permissions = $pdo->query("SELECT * FROM permissions")->fetchAll();

// Fetch Current Role Permissions
$stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = :id");
$stmt->execute(['id' => $role_id]);
$current_perms = $stmt->fetchAll(PDO::FETCH_COLUMN);

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_perms = $_POST['permissions'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        // Clear existing permissions
        $stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = :id");
        $stmt->execute(['id' => $role_id]);
        
        // Insert new permissions
        if (!empty($selected_perms)) {
            $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:rid, :pid)");
            foreach ($selected_perms as $pid) {
                $stmt->execute(['rid' => $role_id, 'pid' => $pid]);
            }
        }
        
        $pdo->commit();
        $msg = "Permissions updated successfully!";
        // Refresh current perms
        $current_perms = $selected_perms;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating permissions: " . $e->getMessage();
    }
}
?>

<div class="content-wrapper">
  <div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Edit Role: <?php echo htmlspecialchars($role['name']); ?></h4>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Manage Permissions</h4>
                        <p class="subtitle mb-0">Select which services this role can access.</p>
                    </div>
                    <div class="box-body">
                        <?php if ($msg): ?>
                            <div class="alert alert-success"><?php echo $msg; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="row">
                                <?php foreach ($permissions as $perm): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="perm_<?php echo $perm['id']; ?>" name="permissions[]" value="<?php echo $perm['id']; ?>" <?php echo in_array($perm['id'], $current_perms) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="perm_<?php echo $perm['id']; ?>">
                                            <strong><?php echo htmlspecialchars($perm['name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($perm['description']); ?></small>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="box-footer mt-4">
                                <button type="submit" class="btn btn-primary">Save Permissions</button>
                                <a href="admin_roles.php" class="btn btn-secondary">Cancel</a>
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