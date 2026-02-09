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
            $sql = "SELECT id, title, start, end, class_name as className, all_day FROM calendar_events";
            if (!$is_admin) {
                $sql .= " WHERE user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['user_id' => $user_id]);
            } else {
                $stmt = $pdo->query($sql);
            }
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format for FullCalendar
            foreach ($events as &$event) {
                $event['allDay'] = (bool)$event['all_day'];
                // Ensure dates are in ISO8601
                $event['start'] = str_replace(' ', 'T', $event['start']);
                if ($event['end']) $event['end'] = str_replace(' ', 'T', $event['end']);
            }
            echo json_encode($events);
            break;

        case 'add':
            $title = $_POST['title'] ?? '';
            $start = $_POST['start'] ?? '';
            $end = $_POST['end'] ?? '';
            $class_name = $_POST['class_name'] ?? 'bg-primary';
            $all_day = (isset($_POST['all_day']) && $_POST['all_day'] === 'true') ? 1 : 0;

            if (empty($title) || empty($start)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            $sql = "INSERT INTO calendar_events (title, start, end, class_name, all_day, user_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $start, $end, $class_name, $all_day, $user_id]);
            echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
            break;

        case 'update':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            
            if (empty($id) || empty($title)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            // Verify ownership if not admin
            if (!$is_admin) {
                $check = $pdo->prepare("SELECT id FROM calendar_events WHERE id = ? AND user_id = ?");
                $check->execute([$id, $user_id]);
                if (!$check->fetch()) die(json_encode(['status' => 'error', 'message' => 'Permission denied']));
            }

            $sql = "UPDATE calendar_events SET title = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $id]);
            echo json_encode(['status' => 'success']);
            break;

        case 'drop': // Handle drag & drop resize/move
            $id = $_POST['id'] ?? 0;
            $start = $_POST['start'] ?? '';
            $end = $_POST['end'] ?? '';
            
            if (empty($id) || empty($start)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            if (!$is_admin) {
                $check = $pdo->prepare("SELECT id FROM calendar_events WHERE id = ? AND user_id = ?");
                $check->execute([$id, $user_id]);
                if (!$check->fetch()) die(json_encode(['status' => 'error', 'message' => 'Permission denied']));
            }

            $sql = "UPDATE calendar_events SET start = ?, end = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$start, $end, $id]);
            echo json_encode(['status' => 'success']);
            break;

        case 'delete':
            $id = $_POST['id'] ?? 0;

            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
                exit;
            }

            if (!$is_admin) {
                $check = $pdo->prepare("SELECT id FROM calendar_events WHERE id = ? AND user_id = ?");
                $check->execute([$id, $user_id]);
                if (!$check->fetch()) die(json_encode(['status' => 'error', 'message' => 'Permission denied']));
            }

            $stmt = $pdo->prepare("DELETE FROM calendar_events WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success']);
            break;

        case 'add_template':
            $title = $_POST['title'] ?? '';
            $class_name = $_POST['class_name'] ?? 'bg-primary';
            
            if (empty($title)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing title']);
                exit;
            }

            $sql = "INSERT INTO event_templates (title, class_name, user_id) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $class_name, $user_id]);
            echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>