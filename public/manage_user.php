<?php
require_once '../src/partials/sidebar.php';

if ($role !== 'super_admin') {
    header('Location: dashboard.php');
    exit();
}

// 3. Ambil pesan flash dari session (jika ada setelah redirect dari aksi)
$message = '';
$message_type = '';
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

?>

<div class="mx-10">
    <div class="h-18 py-11 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Kelola Pengguna</h1>
    </div>

    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 rounded-md <?php echo ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div id="add-user-container" class="flex flex-col bg-white px-6 py-6 rounded-2xl border-2 border-gray-200">
        <h2 class="font-bold text-xl mb-4">Tambah Pengguna Baru</h2>
        <form id="add_user_form" method="POST" action="../src/actions/user_actions.php" class="space-y-4">
            <div class="flex gap-x-7">
                <div class="flex flex-col w-1/2 gap-2">
                    <label for="username" class="font-medium">Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan username" required
                        class="border-2 border-gray-300 rounded-xl p-2">
                </div>
                <div class="flex flex-col w-1/2 gap-2">
                    <label for="password" class="font-medium">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required
                        class="border-2 border-gray-300 rounded-xl  p-2">
                </div>
            </div>
            <div class="flex gap-x-6">
                <div class="flex flex-col w-1/2 gap-2">
                    <label for="role" class="font-medium">Role</label>
                    <select id="role" name="role" required
                        class="border-2 border-gray-300 rounded-xl p-2 text-gray-400">
                        <option value="" class="text-gray-400">Pilih Role</option>
                        <option value="project_manager" class="text-black">Project Manager</option>
                        <option value="team_member" class="text-black">Team Member</option>
                    </select>
                </div>
                <div id="manager-dropdown" class="flex flex-col w-1/2 gap-2">
                    <label id="manager_label" for="project_manager_id" class="font-medium text-gray-300">Manager</label>
                    <select id="project_manager_id" name="project_manager_id"
                        class="border-2 border-gray-300 rounded-xl p-2 cursor-not-allowed text-gray-300" disabled>
                        <option id="manager_option" value="">Hanya untuk Team Member</option>
                        <?php
                        // Query lagi untuk dropdown tambah
                        $sql_managers = "SELECT id, username FROM users WHERE role = 'project_manager' ORDER BY username ASC";
                        $result_managers = $conn->query($sql_managers);
                        if ($result_managers && $result_managers->num_rows > 0) {
                            while ($manager = $result_managers->fetch_assoc()) {
                                echo '<option class="text-black" value="' . $manager['id'] . '">' . htmlspecialchars(ucfirst($manager['username'])) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="tambah_user"
                    class="w-40 mt-2 justify-center items-center bg-blue-600 text-white text-sm font-semibold px-2 py-2 rounded-xl hover:bg-blue-700 transition">
                    + Tambah Pengguna
                </button>
            </div>
        </form>
    </div>

    <div class="mt-2 mb-10 pt-6 border-t border-gray-200">
        <h2 class="font-bold text-xl mb-3">Daftar Pengguna</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Nama Pengguna</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Manajer</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Query untuk mengambil SEMUA user (PM & Member) + nama manajernya
                    $sql_all_users = "
                        SELECT
                            u.id,
                            u.username,
                            u.role,
                            u.project_manager_id, /* Ambil ID manajer untuk data-* */
                            m.username AS manager_name
                        FROM
                            users u
                        LEFT JOIN
                            users m ON u.project_manager_id = m.id
                        WHERE
                            u.role IN ('project_manager', 'team_member')
                        ORDER BY
                            u.id ASC
                    ";
                    $result_all_users = $conn->query($sql_all_users);
                    $counter = 1;

                    if ($result_all_users && $result_all_users->num_rows > 0):
                        while ($user_row = $result_all_users->fetch_assoc()):
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo $counter++; ?></td>
                                <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars(ucfirst($user_row['username'])); ?></td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $user_row['role']))); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo $user_row['manager_name'] ? htmlspecialchars(ucfirst($user_row['manager_name'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button"
                                        class="edit-user-btn inline-block bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md mr-2"
                                        data-id="<?php echo $user_row['id']; ?>"
                                        data-username="<?php echo htmlspecialchars($user_row['username']); ?>"
                                        data-role="<?php echo $user_row['role']; ?>"
                                        data-managerid="<?php echo $user_row['project_manager_id'] ?? ''; ?>">
                                        Edit
                                    </button>
                                    <a href="../src/actions/user_actions.php?delete=<?php echo $user_row['id']; ?>"
                                        class="inline-block bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md"
                                        onclick="return confirmDelete('pengguna <?php echo htmlspecialchars($user_row['username']); ?>');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada Project Manager atau Team Member.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="edit-user-modal" class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full flex items-center justify-center" style="display: none;">
    <div class="relative mx-auto p-8 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4">Edit Pengguna</h3>
        <form id="edit_user_form" method="POST" action="../src/actions/user_actions.php" class="space-y-4">
            <input type="hidden" name="edit_user_id" id="edit_user_id">
            <div>
                <label for="edit_username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="edit_username" id="edit_username" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="edit_password" class="block text-sm font-medium text-gray-700">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="edit_password" id="edit_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="edit_role" class="block text-sm font-medium text-gray-700">Peran (Role)</label>
                <select name="edit_role" id="edit_role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="project_manager">Project Manager</option>
                    <option value="team_member">Team Member</option>
                </select>
            </div>
            <div id="edit-manager-dropdown" style="display: none;">
                <label for="edit_project_manager_id" class="block text-sm font-medium text-gray-700">Manajer Penanggung Jawab</label>
                <select name="edit_project_manager_id" id="edit_project_manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Pilih Manajer --</option>
                    <?php
                    // Query lagi untuk dropdown di modal edit
                    $sql_managers_edit = "SELECT id, username FROM users WHERE role = 'project_manager' ORDER BY username ASC";
                    $result_managers_edit = $conn->query($sql_managers_edit);
                    if ($result_managers_edit && $result_managers_edit->num_rows > 0) {
                        while ($manager_edit = $result_managers_edit->fetch_assoc()) {
                            echo '<option value="' . $manager_edit['id'] . '">' . htmlspecialchars(ucfirst($manager_edit['username'])) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="cancel-edit-btn" class="py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" name="update_user" class="py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../src/partials/footer_tags.php';
?>