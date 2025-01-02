<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
    <link rel="stylesheet" href="css/test.css">
</head>
<body>
    <div class="kanban-board">
        <div class="board-column">
            <div class="column-header">To Do</div>
            <div class="tasks-container" id="todo">
                <div class="task" data-task-id="task1" data-task='{"id": "task1", "title": "Create project plan", "description": "Define project scope and timeline", "deadline": "2024-01-15", "type": "planning"}'>
                    <h3>Create project plan</h3>
                    <p class="task-description">Define project scope and timeline</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 15</span>
                        <span class="type">Planning</span>
                    </div>
                </div>
                <div class="task" data-task-id="task2" data-task='{"id": "task2", "title": "Research competitors", "description": "Analyze market competitors", "deadline": "2024-01-20", "type": "research"}'>
                    <h3>Research competitors</h3>
                    <p class="task-description">Analyze market competitors</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 20</span>
                        <span class="type">Research</span>
                    </div>
                </div>
                <div class="task" data-task-id="task3" data-task='{"id": "task3", "title": "Design wireframes", "description": "Create wireframes for the application", "deadline": "2024-01-22", "type": "design"}'>
                    <h3>Design wireframes</h3>
                    <p class="task-description">Create wireframes for the application</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 22</span>
                        <span class="type">Design</span>
                    </div>
                </div>
                <button class="add-task-btn">+</button>
            </div>
        </div>
        
        <div class="board-column">
            <div class="column-header">In Progress</div>
            <div class="tasks-container" id="inprogress">
                <div class="task" data-task-id="task4" data-task='{"id": "task4", "title": "Implement login system", "description": "Create user authentication", "deadline": "2024-01-25", "type": "development"}'>
                    <h3>Implement login system</h3>
                    <p class="task-description">Create user authentication</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 25</span>
                        <span class="type">Development</span>
                    </div>
                </div>
                <div class="task" data-task-id="task5" data-task='{"id": "task5", "title": "Write documentation", "description": "Write user documentation", "deadline": "2024-01-28", "type": "documentation"}'>
                    <h3>Write documentation</h3>
                    <p class="task-description">Write user documentation</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 28</span>
                        <span class="type">Documentation</span>
                    </div>
                </div>
                <button class="add-task-btn">+</button>
            </div>
        </div>

        <div class="board-column">
            <div class="column-header">Review</div>
            <div class="tasks-container" id="review">
                <div class="task" data-task-id="task6" data-task='{"id": "task6", "title": "Code review needed", "description": "Review pull request #123", "deadline": "2024-01-10", "type": "review"}'>
                    <h3>Code review needed</h3>
                    <p class="task-description">Review pull request #123</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 10</span>
                        <span class="type">Review</span>
                    </div>
                </div>
                <div class="task" data-task-id="task7" data-task='{"id": "task7", "title": "Testing in progress", "description": "Test the application", "deadline": "2024-01-12", "type": "testing"}'>
                    <h3>Testing in progress</h3>
                    <p class="task-description">Test the application</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 12</span>
                        <span class="type">Testing</span>
                    </div>
                </div>
                <button class="add-task-btn">+</button>
            </div>
        </div>
        
        <div class="board-column">
            <div class="column-header">Done</div>
            <div class="tasks-container" id="done">
                <div class="task" data-task-id="task8" data-task='{"id": "task8", "title": "Setup development environment", "description": "Configure development tools", "deadline": "2024-01-05", "type": "setup"}'>
                    <h3>Setup development environment</h3>
                    <p class="task-description">Configure development tools</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 5</span>
                        <span class="type">Setup</span>
                    </div>
                </div>
                <div class="task" data-task-id="task9" data-task='{"id": "task9", "title": "Initial project setup", "description": "Create project repository", "deadline": "2024-01-08", "type": "setup"}'>
                    <h3>Initial project setup</h3>
                    <p class="task-description">Create project repository</p>
                    <div class="task-meta">
                        <span class="deadline">Due: Jan 8</span>
                        <span class="type">Setup</span>
                    </div>
                </div>
                <button class="add-task-btn">+</button>
            </div>
        </div>
    </div>

    <!-- Task Form Modal -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <h2>Add New Task</h2>
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskTitle">Title:</label>
                    <input type="text" id="taskTitle" required>
                </div>
                <div class="form-group">
                    <label for="taskDescription">Description:</label>
                    <textarea id="taskDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label for="taskDeadline">Deadline:</label>
                    <input type="date" id="taskDeadline" required>
                </div>
                <div class="form-group">
                    <label for="taskType">Type:</label>
                    <select id="taskType" required>
                        <option value="Basic">Basic</option>
                        <option value="feature">Feature</option>
                        <option value="bug">bug</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit">Add Task</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
    <script src="js/drag.js"></script>
</body>
</html>