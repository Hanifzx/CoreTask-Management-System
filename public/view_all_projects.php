<?php
require_once '../src/partials/sidebar.php';

if ($role !== 'super_admin') {
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
    <div class="h-18 py-13 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Semua Proyek</h1>
        <p class="font-semibold text-sm text-gray-500">Lihat semua proyek yang ada di sistem</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 rounded-md <?php echo ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="mb-10 pt-3 border-t border-gray-200">
        <div class="overflow-x-auto rounded-2xl border-2 border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Nama Proyek</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tgl Mulai</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Manajer</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tim Terlibat</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $sql_all_projects = "
                        SELECT
                            p.id AS project_id,
                            p.project_name,
                            p.start_date,
                            manager.username AS manager_name,
                            GROUP_CONCAT(DISTINCT member.username ORDER BY member.username SEPARATOR ', ') AS team_members
                        FROM
                            projects p
                        LEFT JOIN
                            users manager ON p.manager_id = manager.id
                        LEFT JOIN
                            tasks t ON p.id = t.project_id
                        LEFT JOIN
                            users member ON t.assigned_to = member.id
                        GROUP BY
                            p.id, p.project_name, p.start_date, manager.username
                        ORDER BY
                            p.id DESC
                    ";
                    $result_all_projects = $conn->query($sql_all_projects);

                    if ($result_all_projects && $result_all_projects->num_rows > 0):
                        while ($project_row = $result_all_projects->fetch_assoc()):
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo $project_row['project_id']; ?></td>
                                <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars(ucfirst($project_row['project_name'])); ?></td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo !empty($project_row['start_date']) ? date('d-m-Y', strtotime($project_row['start_date'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo $project_row['manager_name'] ? htmlspecialchars(ucfirst($project_row['manager_name'])) : 'N/A'; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    <?php echo !empty($project_row['team_members']) ? htmlspecialchars($project_row['team_members']) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="../src/actions/user_actions.php?delete_project=<?php echo $project_row['project_id']; ?>"
                                        class="inline-block bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md"
                                        onclick="return confirmDelete('proyek <?php echo htmlspecialchars($project_row['project_name']); ?>');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada proyek di sistem.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> <?php
        require_once '../src/partials/footer_tags.php';
        ?>