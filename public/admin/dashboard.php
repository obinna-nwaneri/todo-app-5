<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

require_admin();

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $error = 'The task title cannot be empty.';
        } else {
            create_task($pdo, $title, $description !== '' ? $description : null);
            $message = 'Task created successfully.';
        }
    } elseif ($action === 'toggle') {
        $taskId = (int) ($_POST['task_id'] ?? 0);
        $isCompleted = ((int) ($_POST['is_completed'] ?? 0)) === 1;
        update_task_completion($pdo, $taskId, !$isCompleted);
        $message = 'Task updated.';
    } elseif ($action === 'delete') {
        $taskId = (int) ($_POST['task_id'] ?? 0);
        delete_task($pdo, $taskId);
        $message = 'Task deleted.';
    }
}

$tasks = $pdo instanceof PDO ? get_all_tasks($pdo) : [];
$adminName = $_SESSION['admin_display_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin dashboard</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <header class="site-header">
        <div>
            <h1>Admin dashboard</h1>
            <p class="muted">Signed in as <?= htmlspecialchars($adminName); ?></p>
        </div>
        <nav>
            <a href="/">View site</a>
            <a href="/admin/logout.php">Sign out</a>
        </nav>
    </header>

    <main class="layout">
        <section class="panel">
            <h2>Create a task</h2>
            <?php if ($error !== null): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
            <?php elseif ($message !== null): ?>
                <div class="alert"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($dbError !== null): ?>
                <div class="alert alert-error">Database connection error: <?= htmlspecialchars($dbError); ?></div>
            <?php endif; ?>

            <form method="post" class="stack">
                <input type="hidden" name="action" value="create">
                <label>
                    <span>Title</span>
                    <input type="text" name="title" required>
                </label>
                <label>
                    <span>Description <small>(optional)</small></span>
                    <textarea name="description" rows="3"></textarea>
                </label>
                <button type="submit" class="button">Create task</button>
            </form>
        </section>

        <section class="panel">
            <h2>All tasks</h2>
            <div class="task-list">
                <?php if ($pdo === null): ?>
                    <p class="muted">Connect to the database to manage tasks.</p>
                <?php elseif (empty($tasks)): ?>
                    <p class="muted">No tasks yet.</p>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <article class="task <?= $task['is_completed'] ? 'is-complete' : ''; ?>">
                            <header>
                                <div>
                                    <h3><?= htmlspecialchars($task['title']); ?></h3>
                                    <p class="muted">Created <?= htmlspecialchars($task['created_at']); ?></p>
                                </div>
                                <div class="task-actions">
                                    <form method="post">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="task_id" value="<?= (int) $task['id']; ?>">
                                        <input type="hidden" name="is_completed" value="<?= (int) $task['is_completed']; ?>">
                                        <button type="submit" class="button button-secondary">
                                            <?= $task['is_completed'] ? 'Mark incomplete' : 'Mark complete'; ?>
                                        </button>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Delete this task?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="task_id" value="<?= (int) $task['id']; ?>">
                                        <button type="submit" class="button button-danger">Delete</button>
                                    </form>
                                </div>
                            </header>
                            <?php if (!empty($task['description'])): ?>
                                <p><?= htmlspecialchars($task['description']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($task['updated_at']) && $task['updated_at'] !== $task['created_at']): ?>
                                <footer>
                                    <small>Updated <?= htmlspecialchars($task['updated_at']); ?></small>
                                </footer>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
