// Function to create a task card element
function createTaskCard(task) {
    const card = document.createElement('div');
    card.className = 'task-card';
    card.setAttribute('data-task-id', task.id);
    card.setAttribute('draggable', 'true');

    // Create project name display if available
    const projectDisplay = task.project_name ? 
        `<div class="task-project">${escapeHtml(task.project_name)}</div>` : '';

    // Create assigned users display
    const assignedUsersDisplay = task.assignedUsers && task.assignedUsers.length > 0 ?
        `<div class="task-assigned">
            <i class="fas fa-users"></i> 
            ${task.assignedUsers.map(user => escapeHtml(user.username || '')).join(', ')}
        </div>` : '';

    // Ensure all task properties have default values
    const taskData = {
        title: task.title || task.name || 'Untitled Task',
        description: task.description || '',
        state: task.state || 'todo',
        deadline: task.deadline || null,
        tag: task.tag || ''
    };

    card.innerHTML = `
        ${projectDisplay}
        <div class="task-header">${escapeHtml(taskData.title)}</div>
        <div class="task-description">${escapeHtml(taskData.description)}</div>
        <div class="task-meta">
            <span class="task-tag">${escapeHtml(taskData.state)}</span>
            ${taskData.deadline ? `<span class="task-deadline">Due: ${new Date(taskData.deadline).toLocaleDateString()}</span>` : ''}
            ${assignedUsersDisplay}
        </div>
        <div class="task-actions">
            <button class="task-action-btn delete-btn" title="Delete Task">
                <i class="fas fa-trash"></i>
            </button>
            <button class="task-action-btn tag-btn" title="Change Tag">
                <i class="fas fa-tag"></i>
            </button>
            <button class="task-action-btn assign-btn" title="Assign User">
                <i class="fas fa-user"></i>
            </button>
        </div>
    `;

    // Add event listeners for buttons
    const deleteBtn = card.querySelector('.delete-btn');
    const tagBtn = card.querySelector('.tag-btn');
    const assignBtn = card.querySelector('.assign-btn');

    deleteBtn.onclick = (e) => {
        e.stopPropagation();
        if (confirm('Are you sure you want to delete this task?')) {
            deleteTask(task.id);
        }
    };

    tagBtn.onclick = (e) => {
        e.stopPropagation();
        showTagModal(task);
    };

    assignBtn.onclick = (e) => {
        e.stopPropagation();
        showAssignModal(task);
    };

    // Add drag and drop functionality
    card.ondragstart = (e) => {
        e.dataTransfer.setData('text/plain', task.id);
        card.classList.add('dragging');
    };

    card.ondragend = () => {
        card.classList.remove('dragging');
    };

    return card;
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) {
        return '';
    }
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }

    try {
        const response = await fetch('/CRUDTask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'deleteTask',
                id: taskId
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Failed to delete task');
        }

        // Show success message
        const successMessage = document.createElement('div');
        successMessage.className = 'alert alert-success';
        successMessage.textContent = 'Task deleted successfully!';
        document.body.appendChild(successMessage);
        setTimeout(() => successMessage.remove(), 3000);

        // Reload tasks
        loadTasks();
    } catch (error) {
        console.error('Error:', error);
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error';
        errorMessage.textContent = 'Error deleting task. Please try again.';
        document.body.appendChild(errorMessage);
        setTimeout(() => errorMessage.remove(), 3000);
    }
}

function showTagModal(task) {
    // Create and show the tag modal
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'flex';

    const content = document.createElement('div');
    content.className = 'modal-content';

    const header = document.createElement('div');
    header.className = 'modal-header';
    header.innerHTML = '<h2>Change Tag</h2><button class="close-btn">&times;</button>';

    const form = document.createElement('form');
    form.innerHTML = `
        <div class="form-group">
            <label for="newTag">Select New Tag</label>
            <select id="newTag" required>
                <option value="">Select a tag</option>
                <option value="Feature">Feature</option>
                <option value="Bug">Bug</option>
                <option value="Enhancement">Enhancement</option>
                <option value="Documentation">Documentation</option>
                <option value="Testing">Testing</option>
            </select>
        </div>
        <button type="submit" class="submit-btn">Update Tag</button>
    `;

    content.appendChild(header);
    content.appendChild(form);
    modal.appendChild(content);
    document.body.appendChild(modal);

    // Close button handler
    const closeBtn = modal.querySelector('.close-btn');
    closeBtn.onclick = () => {
        modal.remove();
    };

    // Click outside to close
    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    };

    // Form submit handler
    form.onsubmit = async (e) => {
        e.preventDefault();
        const newTag = document.getElementById('newTag').value;
        
        try {
            const response = await fetch('/CRUDTask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'updateTask',
                    id: task.id,
                    project_id: projectId,
                    name: task.name || task.title,
                    description: task.description || '',
                    state: task.state || 'todo',
                    tag: newTag,
                    deadline: task.deadline || null
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Network response was not ok');
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Failed to update tag');
            }

            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'alert alert-success';
            successMessage.textContent = 'Tag updated successfully!';
            document.body.appendChild(successMessage);
            setTimeout(() => successMessage.remove(), 3000);

            // Close modal and reload tasks
            modal.remove();
            loadTasks();
        } catch (error) {
            console.error('Error:', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'alert alert-error';
            errorMessage.textContent = error.message || 'Error updating tag. Please try again.';
            document.body.appendChild(errorMessage);
            setTimeout(() => errorMessage.remove(), 3000);
        }
    };
}

async function showAssignModal(task) {
    try {
        // Get current assignments
        const response = await fetch('/CRUDTask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'getAssignedUsers',
                task_id: task.id
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        const assignedUsers = result.success ? result.data : [];

        // Create and show the assign modal
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.display = 'flex';

        const content = document.createElement('div');
        content.className = 'modal-content';

        const header = document.createElement('div');
        header.className = 'modal-header';
        header.innerHTML = '<h2>Assign Task</h2><button class="close-btn">&times;</button>';

        const assignedList = document.createElement('div');
        assignedList.className = 'assigned-users';
        assignedList.innerHTML = '<h3>Currently Assigned:</h3>';

        if (assignedUsers.length > 0) {
            const usersList = document.createElement('ul');
            usersList.className = 'users-list';
            
            assignedUsers.forEach(user => {
                const userItem = document.createElement('li');
                userItem.innerHTML = `
                    ${user.username}
                    <button class="remove-user-btn" data-user-id="${user.id}">
                        <i class="fas fa-minus-circle"></i>
                    </button>
                `;
                usersList.appendChild(userItem);
            });
            
            assignedList.appendChild(usersList);
        } else {
            assignedList.innerHTML += '<p>No users assigned</p>';
        }

        const form = document.createElement('form');
        form.innerHTML = `
            <div class="form-group">
                <label for="assignee">Assign to</label>
                <input type="text" id="assignee" placeholder="Enter username" required>
            </div>
            <button type="submit" class="submit-btn">Assign Task</button>
        `;

        content.appendChild(header);
        content.appendChild(assignedList);
        content.appendChild(form);
        modal.appendChild(content);
        document.body.appendChild(modal);

        // Handle remove user clicks
        const removeButtons = modal.querySelectorAll('.remove-user-btn');
        removeButtons.forEach(button => {
            button.onclick = async (e) => {
                e.preventDefault();
                const userId = button.dataset.userId;
                
                try {
                    const response = await fetch('/CRUDTask', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'removeAssignment',
                            task_id: task.id,
                            user_id: userId
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const result = await response.json();
                    if (!result.success) {
                        throw new Error(result.message || 'Failed to remove assignment');
                    }

                    // Show success message and refresh modal
                    const successMessage = document.createElement('div');
                    successMessage.className = 'alert alert-success';
                    successMessage.textContent = 'Assignment removed successfully!';
                    document.body.appendChild(successMessage);
                    setTimeout(() => successMessage.remove(), 3000);

                    // Close and reopen modal to refresh assignments
                    modal.remove();
                    showAssignModal(task);
                } catch (error) {
                    console.error('Error:', error);
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-error';
                    errorMessage.textContent = 'Error removing assignment. Please try again.';
                    document.body.appendChild(errorMessage);
                    setTimeout(() => errorMessage.remove(), 3000);
                }
            };
        });

        // Close button handler
        const closeBtn = modal.querySelector('.close-btn');
        closeBtn.onclick = () => {
            modal.remove();
        };

        // Click outside to close
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        };

        // Form submit handler
        form.onsubmit = async (e) => {
            e.preventDefault();
            const assignee = document.getElementById('assignee').value;
            
            try {
                const response = await fetch('/CRUDTask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'assignTask',
                        task_id: task.id,
                        assignee: assignee
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message || 'Failed to assign task');
                }

                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'alert alert-success';
                successMessage.textContent = 'Task assigned successfully!';
                document.body.appendChild(successMessage);
                setTimeout(() => successMessage.remove(), 3000);

                // Close and reopen modal to refresh assignments
                modal.remove();
                showAssignModal(task);
            } catch (error) {
                console.error('Error:', error);
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alert alert-error';
                errorMessage.textContent = error.message || 'Error assigning task. Please try again.';
                document.body.appendChild(errorMessage);
                setTimeout(() => errorMessage.remove(), 3000);
            }
        };
    } catch (error) {
        console.error('Error:', error);
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error';
        errorMessage.textContent = 'Error loading assignments. Please try again.';
        document.body.appendChild(errorMessage);
        setTimeout(() => errorMessage.remove(), 3000);
    }
}

async function loadTasks() {
    try {
        const response = await fetch('/CRUDTask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'getTasksByProject',
                project_id: projectId
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Failed to load tasks');
        }

        // Clear existing tasks
        document.querySelectorAll('.tasks-container').forEach(container => {
            const addButton = container.querySelector('.add-task-btn');
            container.innerHTML = '';
            if (addButton) {
                container.appendChild(addButton);
            }
        });

        // Add tasks to their respective containers
        if (Array.isArray(result.data)) {
            result.data.forEach(task => {
                if (task && task.state) {
                    const container = document.querySelector(`[data-state="${task.state.toLowerCase()}"] .tasks-container`);
                    if (container) {
                        const taskCard = createTaskCard(task);
                        const addButton = container.querySelector('.add-task-btn');
                        container.insertBefore(taskCard, addButton);
                    }
                }
            });
        } else {
            console.error('No tasks data received');
        }
    } catch (error) {
        console.error('Error:', error);
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-error';
        errorMessage.textContent = error.message || 'Error loading tasks. Please try again.';
        document.body.appendChild(errorMessage);
        setTimeout(() => errorMessage.remove(), 3000);
    }
}

// Helper function to get container for state
function getContainerForState(state) {
    const stateToContainer = {
        'To Do': 'todo',
        'In Progress': 'inprogress',
        'Review': 'review',
        'Done': 'done'
    };
    const containerId = stateToContainer[state];
    return containerId ? document.getElementById(containerId) : null;
}

// Initialize dragula for drag and drop
function initializeDragula() {
    const containers = Array.from(document.querySelectorAll('.tasks-container'));
    const drake = dragula(containers, {
        moves: function(el, container, handle) {
            return !handle.classList.contains('add-task-btn'); // Prevent dragging the add button
        }
    });

    // Map container IDs to state names
    const containerIdToState = {
        'todo': 'To Do',
        'inprogress': 'In Progress',
        'review': 'Review',
        'done': 'Done'
    };

    drake.on('drop', async function(el, target, source) {
        const taskId = el.dataset.taskId;
        const newState = containerIdToState[target.id];

        if (!taskId || !newState) {
            console.error('Missing task ID or invalid state');
            loadTasks(); // Revert the move
            return;
        }

        try {
            const response = await fetch('/CRUDTask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'updateTask',
                    id: taskId,
                    state: newState,
                    project_id: projectId
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Failed to update task state');
            }

            // If successful, leave the task in its new position
        } catch (error) {
            console.error('Error updating task state:', error);
            alert('Failed to update task state. The task will return to its original position.');
            loadTasks(); // Revert the move on error
        }
    });

    return drake;
}

// Initialize the board
document.addEventListener('DOMContentLoaded', function() {
    const drake = initializeDragula();
    loadTasks();

    // Modal functionality
    const modal = document.getElementById('taskModal');
    const addButtons = document.querySelectorAll('.add-task-btn');
    const closeBtn = document.querySelector('.close-btn');
    const taskForm = document.getElementById('taskForm');

    // Show modal with column state
    addButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'flex';
            // Store the column state for when we create the task
            taskForm.dataset.column = button.dataset.column;
        });
    });

    // Close modal
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        taskForm.reset();
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
            taskForm.reset();
        }
    });

    // Handle form submission
    taskForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const column = taskForm.dataset.column;
        let state = 'To Do'; // Default state

        // Map column to state
        switch(column) {
            case 'inprogress':
                state = 'In Progress';
                break;
            case 'review':
                state = 'Review';
                break;
            case 'done':
                state = 'Done';
                break;
        }

        const formData = {
            action: 'create',
            project_id: projectId,
            name: document.getElementById('taskName').value,
            description: document.getElementById('taskDescription').value,
            deadline: document.getElementById('taskDeadline').value,
            tag: document.getElementById('taskTag').value,
            state: state
        };

        try {
            const response = await fetch('/CRUDTask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Failed to create task');
            }

            // Reset form and close modal
            taskForm.reset();
            modal.style.display = 'none';

            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'alert alert-success';
            successMessage.textContent = 'Task created successfully!';
            document.body.appendChild(successMessage);
            setTimeout(() => successMessage.remove(), 3000);

            // Reload tasks to show the new task
            loadTasks();
        } catch (error) {
            console.error('Error:', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'alert alert-error';
            errorMessage.textContent = 'Error creating task. Please try again.';
            document.body.appendChild(errorMessage);
            setTimeout(() => errorMessage.remove(), 3000);
        }
    });
});

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}
