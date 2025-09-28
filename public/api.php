<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json');

if (!$pdo instanceof PDO) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection not available.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(get_all_tasks($pdo));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}

$action = $data['action'] ?? '';

switch ($action) {
    case 'create':
        $title = trim((string) ($data['title'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        if ($title === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Title is required.']);
            exit;
        }

        $taskId = create_task($pdo, $title, $description !== '' ? $description : null);
        $task = find_task($pdo, $taskId);
        echo json_encode($task);
        break;

    case 'toggle':
        $taskId = (int) ($data['task_id'] ?? 0);
        $isCompleted = (bool) ($data['is_completed'] ?? false);

        update_task_completion($pdo, $taskId, $isCompleted);
        $task = find_task($pdo, $taskId);
        echo json_encode($task);
        break;

    case 'delete':
        $taskId = (int) ($data['task_id'] ?? 0);
        delete_task($pdo, $taskId);
        echo json_encode(['status' => 'deleted']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action.']);
        break;
}
