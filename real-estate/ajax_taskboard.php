<?php
include 'includes/auth_session.php';
include 'config/db.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$user_id = $_SESSION['user_id'];
$is_admin = hasRole('Admin');

try {
    switch ($action) {
        case 'fetch':
            // Fetch columns first
            $stmt = $pdo->query("SELECT * FROM taskboard_columns ORDER BY position ASC");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch tasks
            $sql = "SELECT t.*, u.username as assigned_to_name FROM tasks t LEFT JOIN users u ON t.user_id = u.id";
            if (!$is_admin) {
                // If not admin, maybe only see own tasks? Or all tasks?
                // For a taskboard, usually everyone sees everything, but let's filter if needed.
                // For now, let's allow everyone to see all tasks to collaborate, but only edit own?
                // Let's stick to "Collaborative" mode: Everyone sees all.
            }
            $sql .= " ORDER BY position ASC, created_at DESC";
            $stmt = $pdo->query($sql);
            $all_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group tasks by status (column slug)
            $tasks_by_column = [];
            foreach ($columns as $col) {
                $tasks_by_column[$col['slug']] = [];
            }
            foreach ($all_tasks as $task) {
                if (isset($tasks_by_column[$task['status']])) {
                    $tasks_by_column[$task['status']][] = $task;
                }
            }

            echo json_encode(['columns' => $columns, 'tasks' => $tasks_by_column]);
            break;

        case 'add':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'todo';
            $priority = $_POST['priority'] ?? 'medium';
            
            if (empty($title)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing title']);
                exit;
            }

            $sql = "INSERT INTO tasks (title, description, status, priority, user_id, position) VALUES (?, ?, ?, ?, ?, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $status, $priority, $user_id]);
            
            echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
            break;

        case 'update_status':
            $id = $_POST['id'] ?? 0;
            $status = $_POST['status'] ?? '';
            // $position = $_POST['position'] ?? 0; // If we want to handle reordering within column

            if (empty($id) || empty($status)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['status' => 'success']);
            break;

        case 'delete':
            $id = $_POST['id'] ?? 0;

            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>