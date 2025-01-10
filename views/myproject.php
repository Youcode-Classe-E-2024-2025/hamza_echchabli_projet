<?php
namespace views;

// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) {
    header('Location: /auth');
    exit();
}
if (!isset($_SESSION['user'])) {
    header('Location: /');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/allStyling.css">
    <link rel="stylesheet" href="css/test.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-tasks"></i>
            <span><a href="/" >KanbanFlow</a></span>
        </div>
        <div class="nav-links">
            <a href="/" > Projects</a>
            <a href="/myproject" class="active">My Projects</a>
            <a href="/mytasks">My Tasks</a>
            <?php echo $_SESSION['user']['username']; ?>
           
            <div class="nav-right">
                <div class="user-menu">
                    <a href='logout'><span>Log Out</span></a>
                </div>
            </div>
        </div>
    </nav>

    <header>
        <h1>My Projects</h1>
        <button class="new-board-btn" id="newProjectBtn">
            <i class="fas fa-plus"></i> Create Project
        </button>
    </header>

    <main class="boards-container">
    <?php
    use models\ProjectModel;

    $res = new ProjectModel();
    $kanbans = $res->getProjectsByUserId($_SESSION['user']['id']);
    
    foreach ($kanbans as $kanban) {
        echo '
        <div class="board-card">
            <div class="board-header">
                <h2>' . htmlspecialchars($kanban['name'] ?? '') . '</h2>
                <div class="board-actions">
                    <button class="btn-state" data-project-id="' . (int)$kanban['id'] . '" data-current-state="' . htmlspecialchars($kanban['type'] ?? 'public') . '">
                        ' . htmlspecialchars($kanban['type'] ?? 'public') . '
                    </button>
                    <button class="btn-delete" data-project-id="' . (int)$kanban['id'] . '">
                        Delete
                    </button>
                </div>
            </div>
            <div class="board-info">
                <div class="leader-info">
                    <div class="leader-details">
                        <span class="leader-name">User ID: ' . htmlspecialchars($kanban['username']) . '</span>
                        <span class="leader-name" style="display: none;">User ID: ' . htmlspecialchars($kanban['user_id']) . '</span>
                       
                        <span class="leader-role">Project Lead</span>
                    </div>
                </div>
                <div class="board-stats">
                    <div class="stat">
                        <span class="stat-number">' . htmlspecialchars($kanban['task_count']) . '</span>
                        <span class="stat-label">Tasks</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">' . htmlspecialchars($kanban['member_count']) . '</span>
                        <span class="stat-label">Members</span>
                    </div>
                </div>
                <a href="kanban?id=' . htmlspecialchars($kanban['id']) . '" class="board-link">View Tasks</a>
            </div>
        </div>';
    }
    ?>
    </main>

    <!-- New Project Modal -->
    <div class="modal" id="newProjectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New Project</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form id="newProjectForm" method="POST" action="">
                <input type="hidden" id="userId" value="<?php echo $_SESSION['user']['id']; ?>">
                <div class="form-group">
                    <label for="projectName">Project Name</label>
                    <input name="name" type="text" id="projectName" required>
                </div>
                <div class="form-group">
                    <label for="projectType">Project Type</label>
                    <select name="type" id="projectType" required>
                        <option value="public">public</option>
                        <option value="private">private</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="projectDescription">Description</label>
                    <textarea name="description" id="projectDescription" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="primary-btn">Create Project</button>
                    <button type="button" class="secondary-btn" id="cancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/project.js"></script>
</body>
</html>
