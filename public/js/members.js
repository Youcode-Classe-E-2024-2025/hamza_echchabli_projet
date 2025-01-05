document.addEventListener('DOMContentLoaded', function() {
    console.log('Members script loaded');
    
    const addMembersBtn = document.getElementById('addMembersBtn');
    if (!addMembersBtn) {
        console.error('Add members button not found');
        return;
    }

    const addMembersModal = document.getElementById('addMembersModal');
    if (!addMembersModal) {
        console.error('Add members modal not found');
        return;
    }

    const closeBtn = addMembersModal.querySelector('.close-btn');
    const userSearchInput = document.getElementById('userSearchInput');
    const searchResults = document.getElementById('searchResults');
    const teamMembersList = document.getElementById('teamMembersList');

    // Get project ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('id');
    console.log('Project ID:', projectId);

    // Show modal
    addMembersBtn.addEventListener('click', function() {
        console.log('Add members button clicked');
        addMembersModal.style.display = 'block';
        loadCurrentMembers();
    });

    // Close modal
    closeBtn.addEventListener('click', function() {
        addMembersModal.style.display = 'none';
    });

    // Search users as typing
    userSearchInput.addEventListener('input', debounce(function() {
        const searchTerm = this.value.trim();
        if (searchTerm.length < 2) {
            searchResults.innerHTML = '';
            return;
        }
        searchUsers(searchTerm);
    }, 300));

    // Search users function
    function searchUsers(term) {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'searchUsers',
                term: term
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                searchResults.innerHTML = '';
                result.data.forEach(user => {
                    const userElement = document.createElement('div');
                    userElement.className = 'user-result';
                    userElement.innerHTML = `
                        <span>${user.username}</span>
                        <button class="add-user-btn" data-user-id="${user.id}">Add</button>
                    `;
                    searchResults.appendChild(userElement);
                });

                // Add click handlers for add buttons
                searchResults.querySelectorAll('.add-user-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const userId = this.dataset.userId;
                        addMemberToProject(userId);
                    });
                });
            } else {
                console.error('Error searching users:', result.message);
            }
        })
        .catch(error => console.error('Error searching users:', error));
    }

    // Add member to project
    function addMemberToProject(userId) {
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
                loadCurrentMembers();
                userSearchInput.value = '';
                searchResults.innerHTML = '';
            } else {
                console.error('Error adding member:', result.message);
            }
        })
        .catch(error => console.error('Error adding member:', error));
    }

    // Load current team members
    function loadCurrentMembers() {
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
                teamMembersList.innerHTML = '';
                result.data.forEach(member => {
                    const memberElement = document.createElement('div');
                    memberElement.className = 'team-member';
                    memberElement.innerHTML = `
                        <span>${member.username}</span>
                        <button class="remove-member-btn" data-user-id="${member.id}">Remove</button>
                    `;
                    teamMembersList.appendChild(memberElement);
                });

                // Add click handlers for remove buttons
                teamMembersList.querySelectorAll('.remove-member-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const userId = this.dataset.userId;
                        removeMemberFromProject(userId);
                    });
                });
            } else {
                console.error('Error loading members:', result.message);
            }
        })
        .catch(error => console.error('Error loading members:', error));
    }

    // Remove member from project
    function removeMemberFromProject(userId) {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'removeMember',
                project_id: projectId,
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadCurrentMembers();
            } else {
                console.error('Error removing member:', result.message);
            }
        })
        .catch(error => console.error('Error removing member:', error));
    }

    // Debounce function to limit API calls
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
