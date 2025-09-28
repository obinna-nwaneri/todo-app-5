const form = document.getElementById('create-task-form');
const taskList = document.getElementById('task-list');
const hasConnection = taskList && taskList.dataset.hasConnection === '1';

async function fetchTasks() {
    if (!hasConnection) {
        return;
    }

    const response = await fetch('/api.php');
    if (!response.ok) {
        console.error('Unable to fetch tasks');
        return;
    }

    const tasks = await response.json();
    renderTasks(tasks);
}

function renderTasks(tasks) {
    if (!taskList) {
        return;
    }

    if (!Array.isArray(tasks)) {
        return;
    }

    taskList.innerHTML = '';

    if (tasks.length === 0) {
        taskList.innerHTML = '<p class="muted">No tasks yet. Add one using the form above.</p>';
        return;
    }

    for (const task of tasks) {
        const article = document.createElement('article');
        article.className = `task ${task.is_completed ? 'is-complete' : ''}`;
        article.dataset.taskId = task.id;

        const header = document.createElement('header');
        const heading = document.createElement('h3');
        heading.textContent = task.title;
        header.appendChild(heading);

        const actions = document.createElement('div');
        actions.className = 'task-actions';

        const toggleButton = document.createElement('button');
        toggleButton.className = 'button button-secondary';
        toggleButton.type = 'button';
        toggleButton.textContent = task.is_completed ? 'Mark incomplete' : 'Mark complete';
        toggleButton.addEventListener('click', () => toggleTask(task.id, !task.is_completed));

        const deleteButton = document.createElement('button');
        deleteButton.className = 'button button-danger';
        deleteButton.type = 'button';
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => {
            if (confirm('Delete this task?')) {
                deleteTask(task.id);
            }
        });

        actions.append(toggleButton, deleteButton);
        header.appendChild(actions);
        article.appendChild(header);

        if (task.description) {
            const description = document.createElement('p');
            description.textContent = task.description;
            article.appendChild(description);
        }

        const footer = document.createElement('footer');
        const created = document.createElement('small');
        created.textContent = `Created ${task.created_at}`;
        footer.appendChild(created);

        if (task.updated_at && task.updated_at !== task.created_at) {
            const updated = document.createElement('small');
            updated.textContent = `Updated ${task.updated_at}`;
            footer.appendChild(updated);
        }

        article.appendChild(footer);
        taskList.appendChild(article);
    }
}

async function createTask(data) {
    const response = await fetch('/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'create', ...data })
    });

    if (!response.ok) {
        alert('Unable to create task.');
        return;
    }

    const task = await response.json();
    form.reset();
    await fetchTasks();
    return task;
}

async function toggleTask(taskId, isCompleted) {
    const response = await fetch('/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'toggle', task_id: taskId, is_completed: isCompleted })
    });

    if (!response.ok) {
        alert('Unable to update task.');
        return;
    }

    await fetchTasks();
}

async function deleteTask(taskId) {
    const response = await fetch('/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'delete', task_id: taskId })
    });

    if (!response.ok) {
        alert('Unable to delete task.');
        return;
    }

    await fetchTasks();
}

if (form && hasConnection) {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(form);
        const title = formData.get('title')?.toString().trim();
        const description = formData.get('description')?.toString().trim();

        if (!title) {
            return;
        }

        await createTask({ title, description });
    });

    fetchTasks();
}
