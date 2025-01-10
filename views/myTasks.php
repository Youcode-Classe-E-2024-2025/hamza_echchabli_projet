
<?php
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
    <title>My Tasks</title>
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
            <a href="/myproject">My Projects</a>
            <a href="/mytasks" class="active">My Tasks</a>
            <?php echo $_SESSION['user']['username']; ?>
            
            <div class="nav-right">
                <div class="user-menu">
                <?php
                    if (isset($_SESSION['user'])) {
                        echo "<a href='logout'><span>Log Out</span></a>"; 
                    } else {
                        echo "<a href='auth'><span>Log in</span></a>"; 
                    }
                ?>
                </div>
            </div>
        </div>
    </nav>

    <header>
        <h1>My Tasks</h1>
    </header>

    <main class="boards-container">
        <?php
        use models\ProjectModel;

        $res = new ProjectModel();
        $projects = $res->getProjectsWithAssignedTasks($_SESSION['user']['id']);

        if (empty($projects)) {
            echo '<div class="no-tasks"><p>You have no assigned tasks in any projects.</p></div>';
        } else {
            foreach ($projects as $project) {
                echo '
                <div class="board-card">
                    <div class="board-header">
                        <h2>' . htmlspecialchars($project['name']) . '</h2>
                        <span class="board-type">' . (!empty($project['state']) ? htmlspecialchars($project['state']) : 'N/A') . '</span>
                    </div>
                    <div class="board-info">
                        <div class="leader-info">
                            <div class="leader-details">
                                <span class="leader-name">Project Lead ID: ' . htmlspecialchars($project['username']) . '</span>
                            </div>
                        </div>
                        <div class="board-stats">
                            <div class="stat">
                                <span class="stat-number">' . $project['task_count'] . '</span>
                                <span class="stat-label">Tasks</span>
                            </div>
                            
                        </div>
                        <a href="kanban?id=' . htmlspecialchars($project['id']) . '" class="board-link">View Tasks</a>
                    </div>
                </div>';
            }
        }
        ?>
    </main>
</body>
</html>