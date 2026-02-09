<?php
include 'config/db.php';

try {
    $permissions = [
        'manage_users', 
        'manage_roles',
        'manage_settings'
    ];
    
    $pdo->beginTransaction();

    foreach ($permissions as $slug) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM permissions WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO permissions (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([ucwords(str_replace('_', ' ', $slug)), $slug, 'Access to ' . str_replace('_', ' ', $slug)]);
            echo "Added permission: $slug<br>";
        }
    }
    
    // Assign these new permissions to Admin role automatically
    $stmt = $pdo->query("SELECT id FROM roles WHERE name = 'Admin'");
    $admin_role_id = $stmt->fetchColumn();
    
    if ($admin_role_id) {
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE slug IN ('" . implode("','", $permissions) . "')");
        $stmt->execute();
        $perm_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($perm_ids as $pid) {
            $stmt->execute([$admin_role_id, $pid]);
        }
        echo "Assigned new permissions to Admin role.<br>";
    }

    $pdo->commit();
    echo "Permissions update completed.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
