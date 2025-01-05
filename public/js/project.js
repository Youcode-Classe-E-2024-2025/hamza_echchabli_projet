document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event fired');

    // Handle project form submission
    const newProjectForm = document.getElementById('newProjectForm');
    if (newProjectForm) {
        console.log('Found project form');
        newProjectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const projectName = document.getElementById('projectName').value;
            const projectType = document.getElementById('projectType').value;
            const projectDescription = document.getElementById('projectDescription').value;
            const userId = document.getElementById('userId').value; // Make sure to add this hidden input in your form

            fetch('/CRUDProject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'create',
                    name: projectName,
                    state: projectType,
                    description: projectDescription,
                    user_id: userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error creating project: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the project');
            });
        });
    }

    // Handle modal
    const newProjectBtn = document.getElementById('newProjectBtn');
    const newProjectModal = document.getElementById('newProjectModal');
    const closeBtn = document.querySelector('.close-btn');
    const cancelBtn = document.getElementById('cancelBtn');

    if (newProjectBtn && newProjectModal) {
        newProjectBtn.addEventListener('click', () => {
            newProjectModal.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            newProjectModal.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            newProjectModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === newProjectModal) {
                newProjectModal.style.display = 'none';
            }
        });
    }

    // Handle project deletion
    const deleteButtons = document.querySelectorAll('.btn-delete');
    console.log('Delete buttons found:', deleteButtons.length);
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Delete button clicked');
            e.preventDefault();
            const projectId = this.getAttribute('data-project-id');
            console.log('Project ID for deletion:', projectId);
            if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                fetch('/CRUDProject', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'deleteProject',
                        id: projectId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Find and remove the project card from DOM
                        const projectCard = this.closest('.board-card');
                        if (projectCard) {
                            projectCard.remove();
                        }
                    } else {
                        alert('Error deleting project: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the project');
                });
            }
        });
    });

    // Handle project state toggle
    const stateButtons = document.querySelectorAll('.btn-state');
    console.log('State buttons found:', stateButtons.length);
    
    stateButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('State button clicked');
            e.preventDefault();
            const projectId = this.getAttribute('data-project-id');
            const currentState = this.getAttribute('data-current-state');
            console.log('Project ID:', projectId);
            const newState = currentState === 'private' ? 'public' : 'private';
            
            fetch('/CRUDProject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: "updateProject",
                    id: projectId,
                    state: newState,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button text and data attribute
                    this.textContent = newState;
                    this.setAttribute('data-current-state', newState);
                } else {
                    alert('Error updating project: ' + data.message); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the project');
            });
        });
    });
});

