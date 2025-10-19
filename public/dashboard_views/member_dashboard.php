<?php
// Total Tugas
$sql_total_tasks = "SELECT COUNT(id) AS total FROM tasks WHERE assigned_to = ?";
$stmt_total = $conn->prepare($sql_total_tasks);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_tasks = $stmt_total->get_result()->fetch_assoc()['total'];
$stmt_total->close();

// Tugas Diproses (Status 'pending' atau 'in_progress')
// Menggunakan 'pending' dan 'in_progress' sebagai status belum selesai
$sql_progress_tasks = "SELECT COUNT(id) AS total FROM tasks WHERE assigned_to = ? AND (status = 'pending' OR status = 'in_progress')";
$stmt_progress = $conn->prepare($sql_progress_tasks);
$stmt_progress->bind_param("i", $user_id);
$stmt_progress->execute();
$progress_tasks = $stmt_progress->get_result()->fetch_assoc()['total'];
$stmt_progress->close();

// Tugas Selesai (Status 'completed')
$sql_completed_tasks = "SELECT COUNT(id) AS total FROM tasks WHERE assigned_to = ? AND status = 'completed'";
$stmt_completed = $conn->prepare($sql_completed_tasks);
$stmt_completed->bind_param("i", $user_id);
$stmt_completed->execute();
$completed_tasks = $stmt_completed->get_result()->fetch_assoc()['total'];
$stmt_completed->close();

?>

<div>
    <div class="h-18 px-8 py-12 mb-3 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Dashboard Team Member</h1>
        <p class="font-semibold text-sm text-gray-500">Berikut ringkasan tugas Anda.</p>
    </div>

    <div id="card-container" class="flex gap-6 mx-8">
        <div class="w-1/3 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Total Tugas</h3>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $total_tasks; ?></h2>
        </div>
        <div class="w-1/3 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Tugas Diproses</h3>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $progress_tasks; ?></h2>
        </div>
        <div class="w-1/3 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Tugas Selesai</h3>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $completed_tasks; ?></h2>
        </div>
    </div>

    <div class="px-8 py-2 mt-8 flex flex-col justify-center items-start">
        <h1 class="font-bold text-xl">Daftar Tugas Anda</h1>
    </div>
    <div class="overflow-x-auto mx-8 mb-10 rounded-2xl border-2 border-gray-200">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-white text-gray-700 border-b border-gray-200 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Proyek</th>
                    <th class="px-6 py-3 text-left">Nama Tugas</th>
                    <th class="px-6 py-3 text-left">Deskripsi</th>
                    <th class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $sql_my_tasks = "
                    SELECT
                        t.id,
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
                            WHEN 'pending' THEN 1
                            WHEN 'in_progress' THEN 2
                            WHEN 'completed' THEN 3
                            ELSE 4
                        END, t.id DESC
                ";
                $stmt_my_tasks = $conn->prepare($sql_my_tasks);
                $stmt_my_tasks->bind_param("i", $user_id);
                $stmt_my_tasks->execute();
                $result_my_tasks = $stmt_my_tasks->get_result();

                if ($result_my_tasks->num_rows > 0):
                    while ($row = $result_my_tasks->fetch_assoc()):
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars($row['project_name']); ?></td>
                        <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($row['task_name']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki tugas.</td></tr>
                <?php
                endif;
                $stmt_my_tasks->close();
                ?>
            </tbody>
        </table>
    </div>
</div>