<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title !== '') {
            create_task($pdo, $title, $description !== '' ? $description : null);
        }
    } elseif ($action === 'toggle') {
        $taskId = (int) ($_POST['task_id'] ?? 0);
        $isCompleted = ((int) ($_POST['is_completed'] ?? 0)) === 1;
        update_task_completion($pdo, $taskId, !$isCompleted);
    } elseif ($action === 'delete') {
        $taskId = (int) ($_POST['task_id'] ?? 0);
        delete_task($pdo, $taskId);
    }

    header('Location: /');
    exit;
}

$tasks = $pdo instanceof PDO ? get_all_tasks($pdo) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <header class="site-header">
        <h1>Todo List</h1>
        <nav>
            <a href="/admin/login.php">Admin</a>
        </nav>
    </header>

    <main class="layout">
        <section class="panel">
            <h2>Add a new task</h2>
            <?php if ($dbError !== null): ?>
                <div class="alert alert-error">
                    Unable to connect to the database: <?= htmlspecialchars($dbError); ?>
                </div>
            <?php endif; ?>
            <form id="create-task-form" method="post" class="stack">
                <input type="hidden" name="action" value="create">
                <label>
                    <span>Title</span>
                    <input type="text" name="title" placeholder="Buy groceries" required>
                </label>
                <label>
                    <span>Description <small>(optional)</small></span>
                    <textarea name="description" rows="3" placeholder="Milk, bread, cheese..."></textarea>
                </label>
                <button type="submit" class="button">Add task</button>
            </form>
        </section>

        <section class="panel">
            <h2>Current tasks</h2>
            <div id="task-list" class="task-list" data-has-connection="<?= $pdo instanceof PDO ? '1' : '0'; ?>">
                <?php if ($pdo === null): ?>
                    <p class="muted">Set up the database connection in <code>config.php</code> to start tracking tasks.</p>
                <?php elseif (empty($tasks)): ?>
                    <p class="muted">No tasks yet. Add one using the form above.</p>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <article class="task <?= $task['is_completed'] ? 'is-complete' : ''; ?>" data-task-id="<?= (int) $task['id']; ?>">
                            <header>
                                <h3><?= htmlspecialchars($task['title']); ?></h3>
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
                            <footer>
                                <small>Created <?= htmlspecialchars($task['created_at']); ?></small>
                                <?php if (!empty($task['updated_at']) && $task['updated_at'] !== $task['created_at']): ?>
                                    <small>Updated <?= htmlspecialchars($task['updated_at']); ?></small>
                                <?php endif; ?>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="/assets/app.js" defer></script>
</body>
</html>
