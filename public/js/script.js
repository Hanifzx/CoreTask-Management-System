// ============================================================
// KONFIRMASI PENGHAPUSAN
// ============================================================
function confirmDelete(message) {
    // Menampilkan kotak konfirmasi sebelum menghapus data.
    return confirm('Apakah Anda yakin ingin menghapus ' + message + '? Tindakan ini tidak dapat dibatalkan.');
}

// Debugging: memastikan script berhasil dimuat di browser
console.log("Script.js berhasil dimuat");

// ============================================================
// MANAJEMEN USER (Hanya untuk Super Admin di manage_user.php)
// ============================================================

// Tangkap form tambah user
const addUserForm = document.getElementById('add_user_form');
if (addUserForm) {
    // Elemen dropdown role dan manager
    const roleDropdown = document.getElementById('role');
    const managerSelect = document.getElementById('project_manager_id');
    const managerLabel = document.getElementById('manager_label');
    const managerOption = document.getElementById('manager_option');

    // Event: ubah warna teks dan aktifkan/disable dropdown manager
    roleDropdown.addEventListener('change', function() {
        // Ubah warna teks role dropdown
        if (this.value) {
            this.classList.remove('text-gray-400');
            this.classList.add('text-black');
        } else {
            this.classList.remove('text-black');
            this.classList.add('text-gray-400');
        }

        // Jika role = team_member → tampilkan dropdown manager
        if (this.value === 'team_member') {
            managerSelect.disabled = false;
            managerOption.textContent = 'Pilih Manager';
            managerLabel.classList.replace('text-gray-300', 'text-black');
            managerSelect.classList.replace('text-gray-300', 'text-black');
            managerSelect.classList.replace('cursor-not-allowed', 'cursor-pointer');
        } else {
            // Selain itu → nonaktifkan dropdown manager
            managerSelect.disabled = true;
            managerOption.textContent = 'Hanya untuk Team Member';
            managerLabel.classList.replace('text-black', 'text-gray-300');
            managerSelect.classList.replace('text-black', 'text-gray-300');
            managerSelect.classList.replace('cursor-pointer', 'cursor-not-allowed');
            managerSelect.value = '';
        }
    });

    // Validasi sebelum form dikirim
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

// ============================================================
// LOGIKA MODAL EDIT USER (Admin/Super Admin)
// ============================================================

const editModal = document.getElementById('edit-user-modal');

if (editModal) {
    // Ambil semua elemen input di modal
    const editForm = document.getElementById('edit_user_form');
    const editUserIdInput = document.getElementById('edit_user_id');
    const editUsernameInput = document.getElementById('edit_username');
    const editPasswordInput = document.getElementById('edit_password');
    const editRoleSelect = document.getElementById('edit_role');
    const editManagerDiv = document.getElementById('edit-manager-dropdown');
    const editManagerSelect = document.getElementById('edit_project_manager_id');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');

    // Fungsi buka modal dan isi data dari tombol edit
    function openEditModal(userData) {
        editUserIdInput.value = userData.id;
        editUsernameInput.value = userData.username;
        editPasswordInput.value = ''; // kosongkan password
        editRoleSelect.value = userData.role;

        // Trigger event untuk tampilkan dropdown manager jika perlu
        editRoleSelect.dispatchEvent(new Event('change'));

        // Isi dropdown manager jika role = team_member
        if (userData.role === 'team_member' && userData.managerid) {
            editManagerSelect.value = userData.managerid;
        } else {
            editManagerSelect.value = '';
        }

        editModal.style.display = 'flex'; // tampilkan modal
    }

    // Tutup modal
    function closeEditModal() {
        editModal.style.display = 'none';
    }

    // Event klik tombol "Edit"
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

    // Tombol batal edit
    cancelEditBtn.addEventListener('click', closeEditModal);

    // Klik di luar area modal → tutup modal
    editModal.addEventListener('click', function(event) {
        if (event.target === editModal) closeEditModal();
    });

    // Ubah tampilan dropdown manager saat role berubah
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

// ============================================================
// LOGIKA MODAL EDIT PROJECT (Project Manager)
// ============================================================

const editProjectModal = document.getElementById('edit-project-modal');

if (editProjectModal) {
    const editProjectForm = document.getElementById('edit_project_form');
    const editProjectIdInput = document.getElementById('edit_project_id');
    const editProjectNameInput = document.getElementById('edit_project_name');
    const editDescriptionInput = document.getElementById('edit_description');
    const cancelEditProjectBtn = document.getElementById('cancel-edit-project-btn');

    // Buka modal edit proyek
    function openEditProjectModal(projectData) {
        editProjectIdInput.value = projectData.id;
        editProjectNameInput.value = projectData.name;
        editDescriptionInput.value = projectData.description;
        editProjectModal.style.display = 'flex';
    }

    // Tutup modal
    function closeEditProjectModal() {
        editProjectModal.style.display = 'none';
    }

    // Klik tombol edit proyek
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

    // Tombol batal edit proyek
    if (cancelEditProjectBtn) {
        cancelEditProjectBtn.addEventListener('click', closeEditProjectModal);
    }

    // Klik di luar area modal → tutup modal
    editProjectModal.addEventListener('click', function(event) {
        if (event.target === editProjectModal) closeEditProjectModal();
    });
}

// ============================================================
// LOGIKA MODAL EDIT TASK (Project Manager)
// ============================================================

const editTaskModal = document.getElementById('edit-task-modal');
const editTaskForm = document.getElementById('edit_task_form');
const editTaskIdInput = document.getElementById('edit_task_id');
const editTaskProjectIdInput = document.getElementById('edit_project_id_for_task');
const editTaskProjectNameDisplay = document.getElementById('edit_task_project_name');
const editTaskNameInput = document.getElementById('edit_task_name');
const editAssignedToSelect = document.getElementById('edit_assigned_to');
const cancelEditTaskBtn = document.getElementById('cancel-edit-task-btn');

// Buka modal edit tugas dan isi data dari tombol
function openEditTaskModal(taskData) {
    editTaskIdInput.value = taskData.id;
    editTaskProjectIdInput.value = taskData.projectId;
    editTaskProjectNameDisplay.textContent = taskData.projectName;
    editTaskNameInput.value = taskData.name;
    editAssignedToSelect.value = taskData.assignedToId || "";
    editTaskModal.style.display = 'flex';
}

// Tutup modal edit tugas
function closeEditTaskModal() {
    editTaskModal.style.display = 'none';
}

// Event tombol "Edit Task"
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

// Tombol batal edit task
if (cancelEditTaskBtn) {
    cancelEditTaskBtn.addEventListener('click', closeEditTaskModal);
}

// Klik di luar area modal edit tugas → tutup
if (editTaskModal) {
    editTaskModal.addEventListener('click', function(event) {
        if (event.target === editTaskModal) closeEditTaskModal();
    });
}
