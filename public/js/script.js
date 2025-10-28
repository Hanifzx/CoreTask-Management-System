function confirmDelete(message) {
    return confirm('Apakah Anda yakin ingin menghapus ' + message + '? Tindakan ini tidak dapat dibatalkan.');
}

console.log("Script.js berhasil dimuat");

// ============================ //
// Manage User Page (for admin) //
// ============================ //

// jika role yang dipilih === 'team_member' saat menambahkan pengguna //
const addUserForm = document.getElementById('add_user_form');
if (addUserForm) {
    const roleDropdown = document.getElementById('role');
    const managerSelect = document.getElementById('project_manager_id');
    const managerLabel = document.getElementById('manager_label');
    const managerOption = document.getElementById('manager_option');

    roleDropdown.addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('text-gray-400');
            this.classList.add('text-black');
            } else {
            this.classList.remove('text-black');
            this.classList.add('text-gray-400');
        }

        if (this.value === 'team_member') {
            managerSelect.disabled = false;
            managerOption.textContent = 'Pilih Manager';
            managerLabel.classList.replace('text-gray-300', 'text-black');
            managerSelect.classList.replace('text-gray-300', 'text-black');
            managerSelect.classList.replace('cursor-not-allowed', 'cursor-pointer');
        } else {
            managerSelect.disabled = true;
            managerOption.textContent = 'Hanya untuk Team Member';
            managerLabel.classList.replace('text-black', 'text-gray-300');
            managerSelect.classList.replace('text-black', 'text-gray-300');
            managerSelect.classList.replace('cursor-pointer', 'cursor-not-allowed');
            managerSelect.value = '';
        }
    });

    // Validasi sebelum submit
    addUserForm.addEventListener('submit', function(e) {
        const roleValue = roleDropdown.value;
        const managerValue = managerSelect.value;

        if (!roleValue) {
            e.preventDefault();
            alert('Silakan pilih Role terlebih dahulu.');
            return;
        }

        if (roleValue === 'team_member' && !managerValue) {
            e.preventDefault();
            alert('Silakan pilih Project Manager untuk Team Member.');
            return;
        }
    });
}

// ============================ //
// EDIT USER MODAL LOGIC        //
// ============================ //

const editModal = document.getElementById('edit-user-modal');

if (editModal) {
    const editForm = document.getElementById('edit_user_form');
    const editUserIdInput = document.getElementById('edit_user_id');
    const editUsernameInput = document.getElementById('edit_username');
    const editPasswordInput = document.getElementById('edit_password');
    const editRoleSelect = document.getElementById('edit_role');
    const editManagerDiv = document.getElementById('edit-manager-dropdown');
    const editManagerSelect = document.getElementById('edit_project_manager_id');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');

    // Fungsi untuk membuka modal dan mengisi data
    function openEditModal(userData) {
        editUserIdInput.value = userData.id;
        editUsernameInput.value = userData.username;
        editPasswordInput.value = '';
        editRoleSelect.value = userData.role;

        editRoleSelect.dispatchEvent(new Event('change'));

        if (userData.role === 'team_member' && userData.managerid) {
            editManagerSelect.value = userData.managerid;
        } else {
            editManagerSelect.value = '';
        }

        editModal.style.display = 'flex';
    }

    function closeEditModal() {
        editModal.style.display = 'none';
    }

    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-user-btn')) {
            const button = event.target;
            const userData = {
                id: button.dataset.id,
                username: button.dataset.username,
                role: button.dataset.role,
                managerid: button.dataset.managerid
            };
            openEditModal(userData);
        }
    });

    cancelEditBtn.addEventListener('click', closeEditModal);

    // Tutup modal jika klik di luar area modal
    editModal.addEventListener('click', function(event) {
        if (event.target === editModal) {
            closeEditModal();
        }
    });

    // Ini untuk show/hide manager dropdown DI DALAM MODAL EDIT
    editRoleSelect.addEventListener('change', function() {
        if (this.value === 'team_member') {
            editManagerDiv.style.display = 'block';
            editManagerSelect.required = true;
        } else {
            editManagerDiv.style.display = 'none';
            editManagerSelect.required = false;
            editManagerSelect.value = '';
        }
    });
}


// ============================ //
// EDIT PROJECT MODAL LOGIC     //
// ============================ //

const editProjectModal = document.getElementById('edit-project-modal');

if (editProjectModal) { 
    const editProjectForm = document.getElementById('edit_project_form');
    const editProjectIdInput = document.getElementById('edit_project_id');
    const editProjectNameInput = document.getElementById('edit_project_name');
    const editDescriptionInput = document.getElementById('edit_description');
    const cancelEditProjectBtn = document.getElementById('cancel-edit-project-btn');

    function openEditProjectModal(projectData) {
        if (!editProjectModal) return;

        editProjectIdInput.value = projectData.id;
        editProjectNameInput.value = projectData.name;
        editDescriptionInput.value = projectData.description;

        editProjectModal.style.display = 'flex';
    }

    function closeEditProjectModal() {
        if (!editProjectModal) return;
        editProjectModal.style.display = 'none';
    }

    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-project-btn')) {
            const button = event.target;
            const projectData = {
                id: button.dataset.id,
                name: button.dataset.name,
                description: button.dataset.description
            };
            openEditProjectModal(projectData);
        }
    });

    if (cancelEditProjectBtn) {
        cancelEditProjectBtn.addEventListener('click', closeEditProjectModal);
    }

    if (editProjectModal) {
        editProjectModal.addEventListener('click', function(event) {
            if (event.target === editProjectModal) {
                closeEditProjectModal();
            }
        });
    }
}


// ============================ //
// EDIT TASK MODAL LOGIC        //
// ============================ //

const editTaskModal = document.getElementById('edit-task-modal');
const editTaskForm = document.getElementById('edit_task_form');
const editTaskIdInput = document.getElementById('edit_task_id');
const editTaskProjectIdInput = document.getElementById('edit_project_id_for_task'); 
const editTaskProjectNameDisplay = document.getElementById('edit_task_project_name'); 
const editTaskNameInput = document.getElementById('edit_task_name');
const editAssignedToSelect = document.getElementById('edit_assigned_to');
const cancelEditTaskBtn = document.getElementById('cancel-edit-task-btn');

// Fungsi untuk membuka modal edit tugas dan mengisi data
function openEditTaskModal(taskData) {
    if (!editTaskModal) return;

    editTaskIdInput.value = taskData.id;
    editTaskProjectIdInput.value = taskData.projectId; 
    editTaskProjectNameDisplay.textContent = taskData.projectName;
    editTaskNameInput.value = taskData.name;
    editAssignedToSelect.value = taskData.assignedToId || ""; // Set ke ID member atau kosong jika null
    editTaskModal.style.display = 'flex';
}

// Fungsi untuk menutup modal edit tugas
function closeEditTaskModal() {
    if (!editTaskModal) return;
    editTaskModal.style.display = 'none';
}

// Tambahkan event listener ke SEMUA tombol edit tugas
document.body.addEventListener('click', function(event) {
    if (event.target.classList.contains('edit-task-btn')) {
        const button = event.target;
        const taskData = {
            id: button.dataset.taskId,
            name: button.dataset.taskName,
            assignedToId: button.dataset.assignedId,
            projectId: button.dataset.projectId,
            projectName: button.dataset.projectName
        };
        openEditTaskModal(taskData);
    }
});

if (cancelEditTaskBtn) {
    cancelEditTaskBtn.addEventListener('click', closeEditTaskModal);
}

// Tutup modal jika klik di luar area modal edit tugas
if (editTaskModal) {
    editTaskModal.addEventListener('click', function(event) {
        if (event.target === editTaskModal) {
            closeEditTaskModal();
        }
    });
}