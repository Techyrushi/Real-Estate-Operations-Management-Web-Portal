<?php
include 'config/db.php';

try {
    // 1. Create roles table
    $sql = "CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    )";
    $pdo->exec($sql);
    echo "Table 'roles' created or exists.\n";

    // 2. Create permissions table
    $sql = "CREATE TABLE IF NOT EXISTS permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    )";
    $pdo->exec($sql);
    echo "Table 'permissions' created or exists.\n";

    // 3. Create role_permissions table
    $sql = "CREATE TABLE IF NOT EXISTS role_permissions (
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        PRIMARY KEY (role_id, permission_id),
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
        FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Table 'role_permissions' created or exists.\n";

    // 4. Update users table columns
    $columns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('full_name', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(100) AFTER username");
        echo "Column 'full_name' added to users.\n";
    }
    if (!in_array('mobile_number', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN mobile_number VARCHAR(20) AFTER email");
        echo "Column 'mobile_number' added to users.\n";
    }
    if (!in_array('profile_image', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) AFTER mobile_number");
        echo "Column 'profile_image' added to users.\n";
    }
    if (!in_array('social_media_links', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN social_media_links TEXT AFTER profile_image");
        echo "Column 'social_media_links' added to users.\n";
    }
    if (!in_array('role_id', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role_id INT AFTER password");
        echo "Column 'role_id' added to users.\n";
        
        // Add FK constraint if not exists (handling via try-catch if it fails due to existing data issues)
        try {
           // $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)");
        } catch (Exception $e) {
            echo "Constraint creation warning: " . $e->getMessage() . "\n";
        }
    }

    // 5. Seed Roles
    $stmt = $pdo->prepare("INSERT IGNORE INTO roles (name, description) VALUES (:name, :desc)");
    $stmt->execute(['name' => 'Admin', 'desc' => 'Full access to all features']);
    $stmt->execute(['name' => 'Agent', 'desc' => 'Can manage properties and view reports']);
    $stmt->execute(['name' => 'User', 'desc' => 'Regular user access']);
    echo "Roles seeded.\n";

    // 6. Seed Permissions
    $permissions = [
        ['name' => 'View Dashboard', 'slug' => 'view_dashboard', 'desc' => 'Access to dashboard'],
        ['name' => 'Manage Users', 'slug' => 'manage_users', 'desc' => 'Create, edit, delete users'],
        ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'desc' => 'Manage roles and permissions'],
        ['name' => 'Manage Properties', 'slug' => 'manage_properties', 'desc' => 'Add, edit, delete properties'],
        ['name' => 'View Reports', 'slug' => 'view_reports', 'desc' => 'View financial reports'],
        ['name' => 'View Profile', 'slug' => 'view_profile', 'desc' => 'View own profile'],
        ['name' => 'Edit Profile', 'slug' => 'edit_profile', 'desc' => 'Edit own profile'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO permissions (name, slug, description) VALUES (:name, :slug, :desc)");
    foreach ($permissions as $perm) {
        $stmt->execute($perm);
    }
    echo "Permissions seeded.\n";

    // 7. Assign Permissions to Admin Role (All permissions)
    $adminRole = $pdo->query("SELECT id FROM roles WHERE name = 'Admin'")->fetch();
    if ($adminRole) {
        $allPerms = $pdo->query("SELECT id FROM permissions")->fetchAll(PDO::FETCH_COLUMN);
        $stmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:rid, :pid)");
        foreach ($allPerms as $pid) {
            $stmt->execute(['rid' => $adminRole['id'], 'pid' => $pid]);
        }
        echo "Admin permissions assigned.\n";
    }

    // 8. Update existing users to have role_id (Default to Admin for 'admin' role, or User otherwise)
    // Assuming 'role' column has values like 'admin', 'agent', etc.
    $pdo->exec("UPDATE users u JOIN roles r ON u.role = r.name SET u.role_id = r.id WHERE u.role_id IS NULL");
    
    // Fallback: If any user has no role_id, assign 'User' role
    $userRole = $pdo->query("SELECT id FROM roles WHERE name = 'User'")->fetch();
    if ($userRole) {
        $pdo->exec("UPDATE users SET role_id = " . $userRole['id'] . " WHERE role_id IS NULL");
    }
    echo "User roles updated.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>