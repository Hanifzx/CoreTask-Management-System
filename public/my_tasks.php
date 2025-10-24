<?php
require_once '../src/config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'team_member') {
    header('Location: dashboard.php');
    exit();
}

require_once '../src/partials/sidebar.php';

$status_colors = [
    'belum' => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-600/20',
    'proses' => 'bg-yellow-100 text-yellow-800 ring-1 ring-inset ring-yellow-600/20',
    'selesai' => 'bg-green-100 text-green-700 ring-1 ring-inset ring-green-600/20'
];

$message = '';
$message_type = '';
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}
?>

<div class="mx-8">
    <div class="h-18 py-12 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Tugas Saya</h1>
        <p class="font-semibold text-sm text-gray-500">Kelola dan perbarui status semua tugas yang diberikan kepada Anda</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="mb-6 p-4 rounded-md <?php echo ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="mb-10 pt-2 border-t border-gray-200">
        <h2 class="font-bold text-xl mb-3">Daftar Semua Tugas Anda</h2>
        <div class="overflow-x-auto rounded-2xl border-2 border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Nama Proyek</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Nama Tugas</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $sql_my_tasks = "
                        SELECT
                            t.id AS task_id,
                            t.task_name,
                            t.description,
                            t.status,
                            p.project_name
                        FROM
                            tasks t
                        JOIN
                            projects p ON t.project_id = p.id
                        WHERE
                            t.assigned_to = ?
                        ORDER BY
                            CASE t.status
                                WHEN 'proses' THEN 1
                                WHEN 'belum' THEN 2
                                WHEN 'selesai' THEN 3
                                ELSE 4
                            END, t.id DESC
                    ";
                    $stmt_my_tasks = $conn->prepare($sql_my_tasks);
                    $stmt_my_tasks->bind_param("i", $user_id);
                    $stmt_my_tasks->execute();
                    $result_my_tasks = $stmt_my_tasks->get_result();

                    if ($result_my_tasks->num_rows > 0):
                        while ($row = $result_my_tasks->fetch_assoc()):
                            $current_status = $row['status'];
                            $color_class = $status_colors[$current_status] ?? 'bg-gray-100 text-gray-700';
                    ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars($row['project_name']); ?></td>
                                <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($row['task_name']); ?></td>
                                <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars(substr($row['description'], 0, 70)) . (strlen($row['description']) > 70 ? '...' : ''); ?></td>
                                
                                <form method="POST" action="../src/actions/task_actions.php">
                                    <input type="hidden" name="task_id" value="<?php echo $row['task_id']; ?>">
                                    
                                    <td class="px-6 py-4">
                                        <select name="new_status" class="task-status-select text-xs font-medium rounded-md px-2 py-1 border-0 focus:ring-1 focus:ring-blue-500 <?php echo $color_class; ?>" data-default-color="<?php echo $color_class; ?>">
                                            <option value="belum" <?php echo ($current_status == 'belum') ? 'selected' : ''; ?>>
                                                Belum
                                            </option>
                                            <option value="proses" <?php echo ($current_status == 'proses') ? 'selected' : ''; ?>>
                                                Proses
                                            </option>
                                            <option value="selesai" <?php echo ($current_status == 'selesai') ? 'selected' : ''; ?>>
                                                Selesai
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="submit" name="update_status"
                                            class="inline-block bg-blue-600 text-white hover:bg-blue-700 px-3 py-1 rounded-md text-xs font-medium">
                                            Update
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki tugas.</td></tr>
                        <?php
                        endif;
                        $stmt_my_tasks->close();
                        ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.task-status-select');
    
    const colorClasses = {
        'belum': 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-600/20',
        'proses': 'bg-yellow-100 text-yellow-800 ring-1 ring-inset ring-yellow-600/20',
        'selesai': 'bg-green-100 text-green-700 ring-1 ring-inset ring-green-600/20'
    };

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            Object.values(colorClasses).forEach(cls => {
                const classes = cls.split(' ');
                classes.forEach(c => select.classList.remove(c));
            });
            
            const newStatus = this.value;
            if (colorClasses[newStatus]) {
                const classes = colorClasses[newStatus].split(' ');
                classes.forEach(c => select.classList.add(c));
            }
        });
    });
});
</script>

<?php
require_once '../src/partials/footer_tags.php';
?>