<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Team Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/allStyling.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#teamMembers">
                                Team Members
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#roles">
                                Roles Management
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
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
                                    <!-- Roles will be loaded here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addMemberForm">
                        <div class="mb-3">
                            <label for="memberName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="memberName" required>
                        </div>
                        <div class="mb-3">
                            <label for="memberEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="memberEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="memberRole" class="form-label">Role</label>
                            <select class="form-control" id="memberRole" required>
                                <!-- Roles will be loaded here dynamically -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveMemberBtn">Save Member</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoleForm">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div id="permissionsCheckboxes">
                                <!-- Permissions checkboxes will be loaded here dynamically -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveRoleBtn">Save Role</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/adminDashboard.js"></script>
</body>
</html>