<?php
require_once '../src/partials/sidebar.php';

if ($role !== 'project_manager') {
    header('Location: dashboard.php');
    exit();
}

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
        <h1 class="font-bold text-2xl">Proyek Saya</h1>
        <p class="font-semibold text-sm text-gray-500">Kelola semua proyek yang Anda pimpin.</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col bg-white px-6 py-6 rounded-2xl border-2 border-gray-200 mb-8">
        <h2 class="font-bold text-xl mb-4">Tambah Proyek Baru</h2>
        <form method="POST" action="../src/actions/project_actions.php" class="space-y-4">
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

    <div class="mb-10 pt-6 border-t border-gray-200">
        <h2 class="font-bold text-xl mb-3">Daftar Proyek Anda</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Nama Proyek</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tgl Mulai</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Deadline</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-700 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Query untuk mengambil SEMUA proyek milik manajer ini
                    $sql_my_projects_list = "SELECT id, project_name, start_date, end_date FROM projects WHERE manager_id = ? ORDER BY id DESC";
                    $stmt_my_list = $conn->prepare($sql_my_projects_list);
                    $stmt_my_list->bind_param("i", $user_id); // $user_id dari sidebar.php
                    $stmt_my_list->execute();
                    $result_my_projects_list = $stmt_my_list->get_result();

                    if ($result_my_projects_list && $result_my_projects_list->num_rows > 0):
                        while ($project_row = $result_my_projects_list->fetch_assoc()):
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($project_row['project_name']); ?></td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo !empty($project_row['start_date']) ? date('d M Y', strtotime($project_row['start_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo !empty($project_row['end_date']) ? date('d M Y', strtotime($project_row['end_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <button type="button" class="inline-block bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded-md mr-2">
                                        Edit
                                    </button>
                                    <a href="../src/actions/project_actions.php?delete=<?php echo $project_row['id']; ?>"
                                        class="inline-block bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md mr-2"
                                        onclick="return confirmDelete('proyek <?php echo htmlspecialchars($project_row['project_name']); ?>');">
                                        Hapus
                                    </a>
                                    <a href="project_tasks.php?project_id=<?php echo $project_row['id']; ?>"
                                        class="inline-block bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded-md">
                                        Kelola Tugas
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Anda belum membuat proyek.</td>
                        </tr>
                    <?php endif;
                    $stmt_my_list->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Panggil tag penutup
require_once '../src/partials/footer_tags.php';
?>