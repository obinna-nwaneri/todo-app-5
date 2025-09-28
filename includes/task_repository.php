<?php
declare(strict_types=1);

/**
 * Fetches all tasks ordered by creation date (newest first).
 */
function get_all_tasks(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, title, description, is_completed, created_at, updated_at FROM todos ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

/**
 * Creates a new todo entry.
 */
function create_task(PDO $pdo, string $title, ?string $description = null): int
{
    $stmt = $pdo->prepare('INSERT INTO todos (title, description) VALUES (:title, :description)');
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Fetches a single todo item by its identifier.
 */
function find_task(PDO $pdo, int $taskId): ?array
{
    $stmt = $pdo->prepare('SELECT id, title, description, is_completed, created_at, updated_at FROM todos WHERE id = :id');
    $stmt->execute([':id' => $taskId]);
    $task = $stmt->fetch();

    return $task === false ? null : $task;
}

/**
 * Updates the completion status of a todo entry.
 */
function update_task_completion(PDO $pdo, int $taskId, bool $completed): void
{
    $stmt = $pdo->prepare('UPDATE todos SET is_completed = :completed, updated_at = NOW() WHERE id = :id');
    $stmt->execute([
        ':id' => $taskId,
        ':completed' => $completed ? 1 : 0,
    ]);
}

/**
 * Deletes a todo entry.
 */
function delete_task(PDO $pdo, int $taskId): void
{
    $stmt = $pdo->prepare('DELETE FROM todos WHERE id = :id');
    $stmt->execute([':id' => $taskId]);
}
