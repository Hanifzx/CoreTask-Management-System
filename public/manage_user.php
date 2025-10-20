<?php
require_once '../src/partials/sidebar.php';

if ($role !== 'super_admin') {
    header('Location: dashboard.php');
    exit();
}

?>

<div class="mx-8">
    <div class="h-18 py-11 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Kelola Pengguna</h1>
    </div>
    <div id="add-user-container" class="flex flex-col bg-white px-6 py-6 rounded-2xl border-2 border-gray-200">
        <h1 class="font-bold text-xl mb-4">Tambah Pengguna Baru</h1>
        <form id="add_user_form" class="space-y-4">
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
                    <select id="role" name="role"
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
                        $sql_managers = "SELECT id, username FROM users WHERE role = 'project_manager' ORDER BY username ASC";
                        $result_managers = $conn->query($sql_managers);
                        if ($result_managers->num_rows > 0) {
                            while ($manager = $result_managers->fetch_assoc()) {
                                echo '<option class="text-black" value="' . $manager['id'] . '">' . htmlspecialchars(ucfirst($manager['username'])) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" form="add_user_form"
                    class="w-1/5 mt-2 justify-center items-center bg-blue-600 text-white font-semibold px-4 py-2 rounded-xl hover:bg-blue-700 transition">
                    + Tambah Pengguna
                </button>
            </div>
        </form>
    </div>
    <div class="mt-10 mb-10 pt-6 border-t border-gray-200">
        <h2 class="font-bold text-xl mb-4">Daftar Pengguna</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">ID</th>
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
                            m.username AS manager_name 
                        FROM 
                            users u 
                        LEFT JOIN 
                            users m ON u.project_manager_id = m.id 
                        WHERE 
                            u.role IN ('project_manager', 'team_member')
                        ORDER BY 
                            u.id ASC 
                    "; // Mengurutkan berdasarkan ID
                    $result_all_users = $conn->query($sql_all_users);
                    $counter = 1; // Untuk nomor urut (#)

                    if ($result_all_users && $result_all_users->num_rows > 0):
                        while ($user_row = $result_all_users->fetch_assoc()):
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo $counter++; ?></td>
                                <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars(ucfirst($user_row['username'])); ?></td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php 
                                    // Mengubah 'project_manager' -> 'Project Manager'
                                    echo htmlspecialchars(ucwords(str_replace('_', ' ', $user_row['role']))); 
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php 
                                    // Tampilkan nama manajer hanya jika ada (untuk team member)
                                    echo $user_row['manager_name'] ? htmlspecialchars(ucfirst($user_row['manager_name'])) : '-'; 
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="inline-block bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md mr-2">Edit</a>
                                    
                                    <a href="manage_users.php?delete=<?php echo $user_row['id']; ?>" 
                                    class="inline-block bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md" 
                                    onclick="return confirmDelete('pengguna <?php echo htmlspecialchars($user_row['username']); ?>');">
                                    Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada Project Manager atau Team Member.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- <div>
    <h1 class="font-bold text-lg"> Daftar Pengguna</h1>
</div> -->

<?php
require_once '../src/partials/footer_tags.php';
?>