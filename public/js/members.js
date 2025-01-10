document.addEventListener('DOMContentLoaded', function() {
    // Get project ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('id');

    // Cache DOM elements
    const teamMembersTable = document.getElementById('teamMembersTable');
    const addMemberBtn = document.getElementById('addMemberBtn');
    const addMembersModal = document.getElementById('addMembersModal');
    const closeBtn = addMembersModal?.querySelector('.close-btn');
    const userSearchInput = document.getElementById('userSearchInput');
    const searchResults = document.getElementById('searchResults');
    const cancelMembersBtn = document.getElementById('cancelMembersBtn');
    const saveMembersBtn = document.getElementById('saveMembersBtn');

    // Initialize
    loadTeamMembers();
    loadRoles();

    // Event Listeners
    if (addMemberBtn) {
        addMemberBtn.addEventListener('click', () => {
            addMembersModal.style.display = 'block';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (cancelMembersBtn) {
        cancelMembersBtn.addEventListener('click', closeModal);
    }

    if (userSearchInput) {
        userSearchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.trim();
            if (searchTerm.length < 2) {
                searchResults.innerHTML = '';
                return;
            }
            searchUsers(searchTerm);
        }, 300));
    }

    // Load Team Members
    function loadTeamMembers() {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'getProjectMembers',
                project_id: projectId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                updateTeamMembersTable(result.data);
            } else {
                showNotification('Error loading team members', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading team members', 'error');
        });
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
                updateRoleSelects(result.data);
            }
        })
        .catch(error => console.error('Error loading roles:', error));
    }

    // Update Team Members Table
    function updateTeamMembersTable(members) {
        if (!teamMembersTable) return;

        teamMembersTable.innerHTML = '';
        members.forEach(member => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(member.username || '')}</td>
                <td>${escapeHtml(member.email || '')}</td>
                <td>
                    <select class="role-select" data-member-id="${member.id || ''}">
                        <!-- Roles will be populated dynamically -->
                    </select>
                </td>
                <td>
                    <button class="btn btn-danger btn-sm remove-member" data-member-id="${member.id || ''}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            const roleSelect = row.querySelector('.role-select');
            updateRoleSelect(roleSelect, member.role_id);

            // Add event listeners
            roleSelect.addEventListener('change', () => {
                updateMemberRole(member.id, roleSelect.value);
            });

            const removeBtn = row.querySelector('.remove-member');
            removeBtn.addEventListener('click', () => {
                removeMember(member.id);
            });

            teamMembersTable.appendChild(row);
        });
    }

    // Update Role Selects
    function updateRoleSelects(rolesData) {
        // Convert roles object to array format needed for selects
        const roles = Object.keys(rolesData).map(roleName => ({
            name: roleName,
            permissions: rolesData[roleName]
        }));

        // Update role selects in the table
        document.querySelectorAll('.role-select').forEach(select => {
            const currentValue = select.value;
            select.innerHTML = `
                <option value="">Select Role</option>
                ${roles.map(role => 
                    `<option value="${escapeHtml(role.name)}">${escapeHtml(role.name)}</option>`
                ).join('')}
            `;
            if (currentValue) {
                select.value = currentValue;
            }
        });
    }

    // Update Single Role Select
    function updateRoleSelect(select, roleId) {
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
                const roles = Object.keys(result.data).map(roleName => ({
                    name: roleName,
                    permissions: result.data[roleName]
                }));
                
                select.innerHTML = `
                    <option value="">Select Role</option>
                    ${roles.map(role => 
                        `<option value="${escapeHtml(role.name)}" ${roleId === role.name ? 'selected' : ''}>
                            ${escapeHtml(role.name)}
                        </option>`
                    ).join('')}
                `;
            }
        })
        .catch(error => console.error('Error loading roles:', error));
    }

    // Search Users
    function searchUsers(term) {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'searchUsers',
                term: term,
                project_id: projectId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                displaySearchResults(result.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error searching users', 'error');
        });
    }

    // Display Search Results
    function displaySearchResults(users) {
        if (!searchResults) return;

        searchResults.innerHTML = '';
        users.forEach(user => {
            const userDiv = document.createElement('div');
            userDiv.className = 'search-result-item';
            userDiv.innerHTML = `
                <span>${escapeHtml(user.username || '')} (${escapeHtml(user.email || '')})</span>
                <button class="btn btn-primary btn-sm add-user" data-user-id="${user.id || ''}">
                    <i class="fas fa-plus"></i> Add
                </button>
            `;

            const addBtn = userDiv.querySelector('.add-user');
            addBtn.addEventListener('click', () => {
                addMember(user.id);
            });

            searchResults.appendChild(userDiv);
        });
    }

    // Add Member
    function addMember(userId) {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'addMember',
                project_id: projectId,
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadTeamMembers();
                closeModal();
                showNotification('Member added successfully', 'success');
            } else {
                showNotification('Failed to add member', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding member', 'error');
        });
    }

    // Update Member Role
    function updateMemberRole(memberId, roleId) {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'updateMemberRole',
                project_id: projectId,
                user_id: memberId,
                role_id: roleId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadTeamMembers();
                showNotification('Role updated successfully', 'success');
            } else {
                showNotification('Failed to update role', 'error');
                loadTeamMembers(); // Reload to revert changes
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating role', 'error');
            loadTeamMembers(); // Reload to revert changes
        });
    }

    // Remove Member
    function removeMember(memberId) {
        if (!confirm('Are you sure you want to remove this member?')) return;

        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'removeMember',
                project_id: projectId,
                user_id: memberId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadTeamMembers();
                showNotification('Member removed successfully', 'success');
            } else {
                showNotification('Failed to remove member', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error removing member', 'error');
        });
    }

    // Utility Functions
    function closeModal() {
        if (addMembersModal) {
            addMembersModal.style.display = 'none';
            if (userSearchInput) userSearchInput.value = '';
            if (searchResults) searchResults.innerHTML = '';
        }
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

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});