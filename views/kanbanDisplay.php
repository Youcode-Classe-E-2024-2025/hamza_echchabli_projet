<?php
namespace views;
use models\TaskModel;
use models\ProjectModel;

// Get project ID from URL
$project_id = $_GET['id'] ?? null;
if (!$project_id) {
    header('Location: /');
    exit();
}

// Get project details
$projectModel = new ProjectModel();
$taskModel = new TaskModel();

?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/allStyling.css">
    <link rel="stylesheet" href="css/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
    <style>
        main {
            padding: 2rem;
            background-color: var(--background-secondary);
            min-height: calc(100vh - 60px);
            margin-top: 60px;
        }

        .sectStyle {
            background-color: var(--background-primary);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .dashboard-section {
            margin-bottom: 2rem;
        }

        .dashboard-section h2 {
            color: var(--text-primary);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: var(--background-primary);
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .table th {
            font-weight: 600;
            background-color: var(--background-secondary);
        }

        .table tr:hover {
            background-color: var(--background-hover);
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-color-dark);
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            main {
                padding: 1rem;
            }

            .sectStyle {
                padding: 1rem;
            }
        }

        /* Permissions Styling */
        .permission-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .permission-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            background-color: var(--background-secondary);
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .permission-group label:hover {
            background-color: var(--background-hover);
        }

        .permission-group input[type="checkbox"] {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-tasks"></i>
            <span><a href="/">KanbanFlow</a></span>
        </div>
        <div class="nav-links">
            <a href="/">Projects</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="/myproject">My Projects</a>
                <span><?php echo $_SESSION['user']['username']; ?></span>
            <?php endif; ?>
            <div class="nav-right">
                <div class="user-menu">
                    <?php
                    if (isset($_SESSION['user'])) {
                        echo "<a href='logout'><span>Log Out</span></a>"; 
                    } else {
                        echo "<a href='auth'><span>Log In</span></a>"; 
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <header>
        <h1>Project name</h1>
        <h1>Project owner</h1>
        <button type="button" id="addMembersBtn">Team members</button>
    </header>

    <div class="kanban-board">
        <!-- Todo Column -->
        <div class="board-column" data-state="todo">
            <div class="column-header">To Do</div>
            <div class="tasks-container" id="todo">
                <button class="add-task-btn" data-column="todo">+</button>
            </div>
        </div>
        
        <!-- In Progress Column -->
        <div class="board-column" data-state="inprogress">
            <div class="column-header">In Progress</div>
            <div class="tasks-container" id="inprogress">
                <button class="add-task-btn" data-column="inprogress">+</button>
            </div>
        </div>

        <!-- Review Column -->
        <div class="board-column" data-state="review">
            <div class="column-header">Review</div>
            <div class="tasks-container" id="review">
                <button class="add-task-btn" data-column="review">+</button>
            </div>
        </div>
        
        <!-- Done Column -->
        <div class="board-column" data-state="done">
            <div class="column-header">Done</div>
            <div class="tasks-container" id="done">
                <button class="add-task-btn" data-column="done">+</button>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Task</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskName">Task Name</label>
                    <input type="text" id="taskName" required>
                </div>
                <div class="form-group">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label for="taskDeadline">Deadline</label>
                    <input type="date" id="taskDeadline" required>
                </div>
                <div class="form-group">
                    <label for="taskTag">Tag</label>
                    <select id="taskTag" required>
                        <option value="">Select a tag</option>
                        <option value="Feature">Feature</option>
                        <option value="Bug">Bug</option>
                        <option value="Enhancement">Enhancement</option>
                        <option value="Documentation">Documentation</option>
                        <option value="Testing">Testing</option>
                    </select>
                </div>
                <!-- <div class="form-group">
                    <label for="assignee">Assign to</label>
                    <select id="assignee" required>
                        <option value="">Select team member</option>
                    </select>
                </div> -->
                <button type="submit">Create Task</button>
            </form>
        </div>
    </div>

    <!-- Add Members Modal -->
    <div id="addMembersModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Team Members</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="search-section">
                    <input type="text" id="userSearchInput" placeholder="Search users...">
                    <div id="searchResults" class="search-results"></div>
                </div>
                <div class="current-members">
                    <h3>Current Team Members</h3>
                    <div id="teamMembersList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="secondary-btn" id="cancelMembersBtn">Cancel</button>
                    <button type="button" class="primary-btn" id="saveMembersBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Role Modal -->
    <div id="roleModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: var(--background-primary); margin: 15% auto; padding: 20px; border-radius: 5px; width: 80%; max-width: 500px;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <h2>Create/Edit Role</h2>
                <button class="close-role-modal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <form id="roleForm" style="margin-top: 15px;">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="roleName" style="display: block; margin-bottom: 5px;">Role Name</label>
                    <input type="text" id="roleName" name="roleName" required style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <h3 style="margin-bottom: 10px;">Permissions</h3>
                    <div class="permission-group" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <label style="display: flex; align-items: center; background-color: var(--background-secondary); padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <input type="checkbox" class="permission-checkbox" value="addMembers" style="margin-right: 5px;"> 
                            Add Members
                        </label>
                        <label style="display: flex; align-items: center; background-color: var(--background-secondary); padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <input type="checkbox" class="permission-checkbox" value="Edit" style="margin-right: 5px;"> 
                            Edit
                        </label>
                        <label style="display: flex; align-items: center; background-color: var(--background-secondary); padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <input type="checkbox" class="permission-checkbox" value="assign" style="margin-right: 5px;"> 
                            Assign
                        </label>
                        <label style="display: flex; align-items: center; background-color: var(--background-secondary); padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <input type="checkbox" class="permission-checkbox" value="create" style="margin-right: 5px;"> 
                            Create
                        </label>
                        <label style="display: flex; align-items: center; background-color: var(--background-secondary); padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <input type="checkbox" class="permission-checkbox" value="delete" style="margin-right: 5px;"> 
                            Delete
                        </label>
                        <label style="display: flex; align-items: center; background-color: var(--background-secondary); padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <input type="checkbox" class="permission-checkbox" value="removeMember" style="margin-right: 5px;"> 
                            Remove Member
                        </label>
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color); color: white; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">Save Role</button>
                    <button type="button" id="cancelRoleBtn" class="btn btn-secondary" style="background-color: var(--background-secondary); color: var(--text-primary); padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">Cancel</button>
                   
                </div>
            </form>
        </div>
    </div>

    <div id="addMembersModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Team Members</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="search-section">
                    <input type="text" id="userSearchInput" placeholder="Search users...">
                    <div id="searchResults" class="search-results"></div>
                </div>
                <div class="current-members">
                    <h3>Current Team Members</h3>
                    <div id="teamMembersList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="secondary-btn" id="cancelMembersBtn">Cancel</button>
                    <button type="button" class="primary-btn" id="saveMembersBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <section id="left" class="sectStyle">
                    <!-- Team Members Section -->
                    <div id="teamMembers" class="dashboard-section">
                        <h2>Team Members</h2>
                        <div class="mb-3">
                            <button class="btn btn-primary" id="addMemberBtn">Add New Member</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="teamMembersTable">
                                    <!-- Team members will be loaded here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Roles Management Section -->
                    <div id="roles" class="dashboard-section mt-4">
                        <h2>Roles Management</h2>
                        <div class="mb-3">
                            <button class="btn btn-primary" id="addRoleBtn">Create New Role</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Role Name</th>
                                        <th>Permissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="rolesTable">

                                
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>

    <script>
        // Store project ID for JavaScript use
        const projectId = <?php echo json_encode($project_id); ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
    <script src="js/kanban.js"></script>
    <script src="js/members.js"></script>
    <script src="js/roles.js"></script>
    
</body>
</html>