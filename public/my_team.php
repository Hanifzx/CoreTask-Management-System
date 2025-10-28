<?php
require_once '../src/config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];

if ($role !== 'project_manager') {
    header('Location: dashboard.php');
    exit();
}

require_once '../src/partials/sidebar.php';

global $conn;
$manager_id = $_SESSION['user_id'];
?>

<div class="mx-8">
    <div class="h-18 py-12 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Tim Saya</h1>
        <p class="font-semibold text-sm text-gray-500">Lihat anggota tim dan tugas yang sedang mereka kerjakan.</p>
    </div>

    <?php
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $message_type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        echo '<div class="mb-6 p-4 rounded-md ' . ($message_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') . '">' . htmlspecialchars($message) . '</div>';
    }
    ?>

    <div class="mb-10 border-t border-gray-200">
        <h2 class="font-bold text-xl mb-3">Daftar Anggota Tim Anda</h2>
        <div class="overflow-x-auto rounded-2xl border-2 border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Nama Member</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Proyek Dikerjakan</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Tugas Aktif</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-700 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $sql_team_details = "
                        SELECT
                            u.id AS member_id,
                            u.username AS member_name,
                            COUNT(DISTINCT t.project_id) AS total_projects, -- Hitung jumlah proyek unik
                            COUNT(CASE WHEN t.status != 'completed' AND t.status != 'selesai' THEN t.id END) AS active_tasks -- Hitung tugas aktif
                        FROM
                            users u
                        LEFT JOIN
                            tasks t ON u.id = t.assigned_to
                        LEFT JOIN
                            projects p ON t.project_id = p.id AND p.manager_id = ? -- Pastikan proyek milik manajer ini
                        WHERE
                            u.role = 'team_member' AND u.project_manager_id = ?
                        GROUP BY
                            u.id, u.username
                        ORDER BY
                            u.username ASC
                    ";
                    
                    $stmt_team_details = $conn->prepare($sql_team_details);
                    $stmt_team_details->bind_param("ii", $manager_id, $manager_id);
                    $stmt_team_details->execute();
                    $result_team_details = $stmt_team_details->get_result();

                    if ($result_team_details && $result_team_details->num_rows > 0):
                        while ($member_row = $result_team_details->fetch_assoc()):
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?php echo htmlspecialchars(ucfirst($member_row['member_name'])); ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo $member_row['total_projects']; ?> Proyek
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?php echo $member_row['active_tasks']; ?> Tugas Aktif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <button type="button" 
                                        class="inline-block bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded-md"
                                        onclick="alert('Fitur Tambah Tugas via pop-up belum diimplementasikan.');"> 
                                        + Tugaskan
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki anggota tim.</td>
                        </tr>
                    <?php endif;
                    $stmt_team_details->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../src/partials/footer_tags.php';
?>