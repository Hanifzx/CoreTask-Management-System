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