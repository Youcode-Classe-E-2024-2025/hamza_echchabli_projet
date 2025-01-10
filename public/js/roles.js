document.addEventListener('DOMContentLoaded', function() {
    // Get project ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('id');

    // Cache DOM elements
    const rolesTable = document.getElementById('rolesTable');
    const addRoleBtn = document.getElementById('addRoleBtn');
    const roleModal = document.getElementById('roleModal');
    const roleForm = document.getElementById('roleForm');
    const closeRoleModalBtn = document.querySelector('.close-role-modal');

    // Initialize
    loadRoles();

    // Event Listeners
    if (addRoleBtn) {
        addRoleBtn.addEventListener('click', () => {
            openRoleModal();
        });
    }

    if (closeRoleModalBtn) {
        closeRoleModalBtn.addEventListener('click', closeRoleModal);
    }

    if (roleForm) {
        roleForm.addEventListener('submit', handleRoleSubmit);
    }

    // Load Roles
    function loadRoles() {
        fetch('/roles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'getProjectRoles',
                project_id: projectId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateRolesTable(result.data);
            } else {
                showNotification('Error loading roles', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading roles', 'error');
        });
    }

    // Update Roles Table
    function updateRolesTable(rolesData) {
        if (!rolesTable) return;

        rolesTable.innerHTML = '';
        
        // Convert the object to an array of role entries
        const roles = Object.entries(rolesData).map(([roleName, permissions]) => ({
            name: roleName,
            permissions: permissions
        }));

        roles.forEach(role => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(role.name)}</td>
                <td>${formatPermissions(role.permissions)}</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-role" data-role-name="${role.name}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete-role" data-role-name="${role.name}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            `;

            // Add event listeners
            const editBtn = row.querySelector('.edit-role');
            const deleteBtn = row.querySelector('.delete-role');

            editBtn.addEventListener('click', () => editRole(role));
            deleteBtn.addEventListener('click', () => deleteRole(role.name));

            rolesTable.appendChild(row);
        });
    }

    // Format Permissions for Display
    function formatPermissions(permissions) {
        if (!permissions || permissions.length === 0) {
            return '<span class="text-muted">No permissions</span>';
        }
        return permissions.map(perm => 
            `<span class="badge bg-primary me-1">${escapeHtml(perm)}</span>`
        ).join('');
    }

    // Handle Role Form Submit
    function handleRoleSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(roleForm);
        const roleName = formData.get('roleName');
        const roleId = roleForm.dataset.roleId;

        // Get selected permissions
        const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
            .map(checkbox => checkbox.value);

        const requestData = {
            action: roleId ? 'updateRole' : 'createRole',
            role_name: roleName,
            project_id: projectId,
            permissions: selectedPermissions
        };

        fetch('/roles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadRoles();
                closeRoleModal();
                showNotification(`Role ${roleId ? 'updated' : 'created'} successfully`, 'success');
            } else {
                showNotification(result.message || `Failed to ${roleId ? 'update' : 'create'} role`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(`Error ${roleId ? 'updating' : 'creating'} role`, 'error');
        });
    }

    // Edit Role
    function editRole(role) {
        if (!roleForm || !roleModal) return;

        document.getElementById('roleName').value = role.name;
        roleForm.dataset.roleId = role.name; // Using role name as identifier

        // Set permissions
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = role.permissions.includes(checkbox.value);
        });

        openRoleModal();
    }

    // Delete Role
    function deleteRole(roleName) {
        if (!confirm('Are you sure you want to delete this role?')) return;

        fetch('/roles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'deleteRole',
                role_name: roleName,
                project_id: projectId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadRoles();
                showNotification('Role deleted successfully', 'success');
            } else {
                showNotification(result.message || 'Failed to delete role', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting role', 'error');
        });
    }

    // Utility Functions
    function openRoleModal() {
        roleModal.style.display = 'block';
    }

    function closeRoleModal() {
        roleModal.style.display = 'none';
        roleForm.reset();
        delete roleForm.dataset.roleId;
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    function escapeHtml(unsafe) {
        if (unsafe === undefined || unsafe === null) {
            return '';
        }
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});