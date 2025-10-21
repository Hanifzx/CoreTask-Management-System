<?php
require_once '../src/config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 3. Ambil role
$role = $_SESSION['role'];

// 4. SEKARANG, cek hak akses (otorisasi)
if ($role !== 'project_manager') {
    // Jika bukan PM, redirect SEBELUM HTML dicetak
    header('Location: dashboard.php');
    exit();
}

require_once '../src/partials/sidebar.php';

$message = '';
$message_type = '';
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

global $conn;
?>

<div class="mx-8">
    <div class="h-18 py-12 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Proyek Saya</h1>
        <p class="font-semibold text-sm text-gray-500">Kelola semua proyek yang Anda pimpin.</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col bg-white px-5 py-5 rounded-2xl border-2 border-gray-200 mt-2 mb-8">
        <h2 class="font-bold text-xl mb-4">Tambah Proyek Baru</h2>
        <form method="POST" action="../src/actions/project_actions.php" class="space-y-3">
            <div class="flex flex-col gap-2">
                <label for="project_name" class="font-medium">Nama Proyek <span class="text-red-500">*</span></label>
                <input type="text" name="project_name" id="project_name" placeholder="Masukkan nama proyek" required
                    class="border-2 border-gray-300 rounded-xl p-2 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex flex-col gap-2">
                <label for="description" class="font-medium">Deskripsi</label>
                <textarea name="description" id="description" rows="3" placeholder="Masukkan deskripsi singkat proyek"
                    class="border-2 border-gray-300 rounded-xl p-2 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="add_project"
                    class="w-40 mt-2 justify-center items-center bg-blue-600 text-white text-sm font-semibold px-2 py-2 rounded-xl hover:bg-blue-700 transition">
                    + Tambah Proyek
                </button>
            </div>
        </form>
    </div>

    <div class="mb-10 border-t border-gray-200">
        <h2 class="font-bold text-xl mb-3">Daftar Proyek Anda</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Nama Proyek</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tgl Mulai</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tgl Selesai</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-700 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $sql_my_projects_list = "SELECT id, project_name, description, start_date, end_date FROM projects WHERE manager_id = ? ORDER BY id DESC";
                    $stmt_my_list = $conn->prepare($sql_my_projects_list);
                    $stmt_my_list->bind_param("i", $user_id);
                    $stmt_my_list->execute();
                    $result_my_projects_list = $stmt_my_list->get_result();

                    if ($result_my_projects_list && $result_my_projects_list->num_rows > 0):
                        while ($project_row = $result_my_projects_list->fetch_assoc()):
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($project_row['project_name']); ?></td>
                                <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars(substr($project_row['description'], 0, 50)) . (strlen($project_row['description']) > 50 ? '...' : ''); ?></td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo !empty($project_row['start_date']) ? date('d M Y', strtotime($project_row['start_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo !empty($project_row['end_date']) ? date('d M Y', strtotime($project_row['end_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <button type="button"
                                        class="edit-project-btn inline-block bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded-md mr-2"
                                        data-id="<?php echo $project_row['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($project_row['project_name']); ?>"
                                        data-description="<?php echo htmlspecialchars($project_row['description']); ?>">
                                        Edit
                                    </button>
                                    <a href="../src/actions/project_actions.php?delete=<?php echo $project_row['id']; ?>"
                                        class="inline-block bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md mr-2"
                                        onclick="return confirmDelete('proyek <?php echo htmlspecialchars($project_row['project_name']); ?>');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Anda belum membuat proyek.</td>
                        </tr>
                    <?php endif;
                    $stmt_my_list->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="edit-project-modal" class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full flex items-center justify-center" style="display: none;">
    <div class="relative mx-auto p-8 border w-full max-w-lg shadow-lg rounded-xl bg-white">
        <h3 class="text-xl font-bold mb-4">Edit Proyek</h3>
        <form id="edit_project_form" method="POST" action="../src/actions/project_actions.php" class="space-y-4">
            <input type="hidden" name="edit_project_id" id="edit_project_id">

            <div class="flex flex-col gap-2">
                <label for="edit_project_name" class="font-medium">Nama Proyek <span class="text-red-500">*</span></label>
                <input type="text" name="edit_project_name" id="edit_project_name" required
                    class="border-2 border-gray-300 rounded-xl p-2 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex flex-col gap-2">
                <label for="edit_description" class="font-medium">Deskripsi</label>
                <textarea name="edit_description" id="edit_description" rows="3"
                    class="border-2 border-gray-300 rounded-xl p-2 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="cancel-edit-project-btn" class="py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" name="update_project" class="py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>


<?php
require_once '../src/partials/footer_tags.php';
?>