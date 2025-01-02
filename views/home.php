<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Kanban Boards</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/allStyling.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-tasks"></i>
            <span>KanbanFlow</span>
        </div>
        <div class="nav-links">
            <a href="#" class="active">My Projects</a>
            <a href="#">Teams</a>
            <div class="nav-right">
                <button id="theme-toggle">
                    <i class="fas fa-moon"></i>
                </button>
                <div class="user-menu">
                    <img src="https://ui-avatars.com/api/?name=User" alt="User" class="user-avatar">
                    <span>John Doe</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>
    </nav>

    <header>
        <h1>My Projects</h1>
        <button class="new-board-btn" id="newBoardBtn">+ New Board</button>
    </header>

    <main class="boards-container">
        <div class="board-card">
            <div class="board-header">
                <h2>Marketing Campaign</h2>
                <span class="board-type">Marketing</span>
            </div>
            <div class="board-info">
                <div class="leader-info">
                    <img src="https://ui-avatars.com/api/?name=John+Doe" alt="John Doe" class="leader-avatar">
                    <div class="leader-details">
                        <span class="leader-name">John Doe</span>
                        <span class="leader-role">Project Lead</span>
                    </div>
                </div>
                <div class="board-stats">
                    <div class="stat">
                        <span class="stat-number">12</span>
                        <span class="stat-label">Tasks</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">4</span>
                        <span class="stat-label">Members</span>
                    </div>
                </div>
            </div>
            <a href="kanban" class="board-link"></a>
        </div>

        <div class="board-card">
            <div class="board-header">
                <h2>Website Redesign</h2>
                <span class="board-type">Development</span>
            </div>
            <div class="board-info">
                <div class="leader-info">
                    <img src="https://ui-avatars.com/api/?name=Sarah+Smith" alt="Sarah Smith" class="leader-avatar">
                    <div class="leader-details">
                        <span class="leader-name">Sarah Smith</span>
                        <span class="leader-role">Tech Lead</span>
                    </div>
                </div>
                <div class="board-stats">
                    <div class="stat">
                        <span class="stat-number">8</span>
                        <span class="stat-label">Tasks</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">3</span>
                        <span class="stat-label">Members</span>
                    </div>
                </div>
            </div>
            <a href="kanban" class="board-link"></a>
        </div>
    </main>

    <!-- New Board Modal -->
    <div class="modal" id="newBoardModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New Board</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form id="newBoardForm">
                <div class="form-group">
                    <label for="boardName">Board Name</label>
                    <input type="text" id="boardName" required>
                </div>
                <div class="form-group">
                    <label for="boardType">Board Type</label>
                    <select id="boardType" required>
                        <option value="development">Development</option>
                        <option value="marketing">Marketing</option>
                        <option value="operations">Operations</option>
                        <option value="product">Product</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="boardDescription">Description</label>
                    <textarea id="boardDescription" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="primary-btn">Create Board</button>
                    <button type="button" class="secondary-btn" id="cancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            themeToggle.innerHTML = newTheme === 'dark' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
        });

        // Modal handling
        const modal = document.getElementById('newBoardModal');
        const newBoardBtn = document.getElementById('newBoardBtn');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.getElementById('cancelBtn');
        const newBoardForm = document.getElementById('newBoardForm');

        newBoardBtn.addEventListener('click', () => {
            modal.classList.add('show');
        });

        function closeModal() {
            modal.classList.remove('show');
            newBoardForm.reset();
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        newBoardForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Handle form submission here
            const boardData = {
                name: document.getElementById('boardName').value,
                type: document.getElementById('boardType').value,
                description: document.getElementById('boardDescription').value
            };
            console.log('New board data:', boardData);
            closeModal();
        });
    </script>
</body>
</html>