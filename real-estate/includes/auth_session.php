<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth_login.php");
    exit();
}

// Check for session timeout (e.g., 30 minutes)
$timeout_duration = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: auth_login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

function hasRole($roleName) {
    if (!isset($_SESSION['role_name'])) {
        return false;
    }
    return strtolower($_SESSION['role_name']) === strtolower($roleName);
}

function hasPermission($slug) {
    if (hasRole('Admin')) {
        return true;
    }
    if (!isset($_SESSION['permissions'])) {
        return false;
    }
    return in_array($slug, $_SESSION['permissions']);
}
?>
