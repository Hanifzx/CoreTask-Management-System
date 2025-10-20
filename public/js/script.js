/**
 * Menampilkan dialog konfirmasi sebelum menghapus.
 * @param {string} message Pesan spesifik (misal: 'pengguna ini')
 * @returns {boolean} True jika OK, false jika Cancel.
*/
function confirmDelete(message) {
    return confirm('Apakah Anda yakin ingin menghapus ' + message + '? Tindakan ini tidak dapat dibatalkan.');
}

console.log("Script.js berhasil dimuat");

// ============================ //
// Manage User Page (for admin) //
// ============================ //

// Pengaturan dropdown Role = Project Manajer //
// jika role yang dipilih === 'team_member' pada manage_user.php //
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

        // Cegah submit jika role belum dipilih
        if (!roleValue) {
            e.preventDefault();
            alert('Silakan pilih Role terlebih dahulu.');
            return;
        }

        // Cegah submit jika role = team_member tapi manager belum dipilih
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

// Dapatkan elemen-elemen modal
const editModal = document.getElementById('edit-user-modal');
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
    editPasswordInput.value = ''; // Kosongkan password saat membuka
    editRoleSelect.value = userData.role;

    // Picu event 'change' agar dropdown manager tampil/sembunyi
    editRoleSelect.dispatchEvent(new Event('change'));

    // Set nilai manager jika ada
    if (userData.role === 'team_member' && userData.managerid) {
        editManagerSelect.value = userData.managerid;
    } else {
        editManagerSelect.value = '';
    }

    editModal.style.display = 'flex'; // Tampilkan modal
}

// Fungsi untuk menutup modal
function closeEditModal() {
    editModal.style.display = 'none';
}

// Tambahkan event listener ke SEMUA tombol edit di tabel
// Kita gunakan event delegation pada body atau tabel container untuk efisiensi
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

// Tambahkan event listener untuk tombol Batal
cancelEditBtn.addEventListener('click', closeEditModal);

// (Optional) Tutup modal jika klik di luar area modal
editModal.addEventListener('click', function(event) {
    if (event.target === editModal) { // Cek jika target klik adalah latar belakang modal
        closeEditModal();
    }
});

// Tambahkan event listener untuk show/hide manager dropdown DI DALAM MODAL EDIT
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