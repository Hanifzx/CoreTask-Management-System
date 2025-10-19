<?php

// Mengambil jumlah proyek yang dimiliki oleh manajer ini
$sql_total_my_projects = "SELECT COUNT(id) AS total FROM projects WHERE manager_id = ?";
$stmt_my_projects = $conn->prepare($sql_total_my_projects);
$stmt_my_projects->bind_param("i", $user_id);
$stmt_my_projects->execute();
$total_my_projects = $stmt_my_projects->get_result()->fetch_assoc()['total'];
$stmt_my_projects->close();

// Mengambil jumlah team member yang melapor ke manajer ini
$sql_total_my_team = "SELECT COUNT(id) AS total FROM users WHERE project_manager_id = ?";
$stmt_my_team = $conn->prepare($sql_total_my_team);
$stmt_my_team->bind_param("i", $user_id);
$stmt_my_team->execute();
$total_my_team = $stmt_my_team->get_result()->fetch_assoc()['total'];
$stmt_my_team->close();

?>

<div>
    <div class="h-18 px-8 py-12 mb-3 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Dashboard Project Manager</h1>
        <p class="font-semibold text-sm text-gray-500">Berikut ringkasan proyek dan tim Anda.</p>
    </div>

    <!-- Kartu Statistik -->
    <div id="card-container" class="flex gap-6 mx-8">
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Proyek Anda</h3>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $total_my_projects; ?></h2>
        </div>
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Anggota Tim Anda</h3>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $total_my_team; ?></h2>
        </div>
    </div>

    <!-- Tabel Proyek Terkini -->
    <div class="px-8 py-2 mt-8 flex flex-col justify-center items-start">
        <h1 class="font-bold text-xl">Proyek Terkini Anda</h1>
    </div>
    <div class="overflow-x-auto mx-8 rounded-2xl border-2 border-gray-200">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-white text-gray-700 border-b border-gray-200 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Nama Proyek</th>
                    <th class="px-6 py-3 text-left">Deskripsi</th>
                    <th class="px-6 py-3 text-center">End Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $sql_projects = "SELECT id, project_name, description, end_date FROM projects WHERE manager_id = ? ORDER BY id DESC";
                $stmt_projects = $conn->prepare($sql_projects);
                $stmt_projects->bind_param("i", $user_id);
                $stmt_projects->execute();
                $result_projects = $stmt_projects->get_result();

                if ($result_projects->num_rows > 0):
                    while ($row = $result_projects->fetch_assoc()):
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium"><?php echo $row['id']; ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['project_name']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars(substr($row['description'], 0, 50)) . '...'; ?></td>
                        <td class="px-6 py-4 text-center"><?php echo !empty($row['end_date']) ? date('d-m-Y', strtotime($row['end_date'])) : '-'; ?></td>
                    </tr>
                <?php 
                    endwhile;
                else: 
                ?>
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki proyek.</td></tr>
                <?php 
                endif;
                $stmt_projects->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Tabel Daftar Tugas -->
    <div class="px-8 py-2 mt-8 flex flex-col justify-center items-start">
        <h1 class="font-bold text-xl">Daftar Tugas Terkini</h1>
    </div>
    <div class="overflow-x-auto mx-8 mb-10 rounded-2xl border-2 border-gray-200">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-white text-gray-700 border-b border-gray-200 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Nama Tugas</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Ditugaskan Kepada</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $sql_tasks = "
                    SELECT t.id, t.task_name, t.status, u.username AS assigned_to_name
                    FROM tasks t
                    JOIN projects p ON t.project_id = p.id
                    JOIN users u ON t.assigned_to = u.id
                    WHERE p.manager_id = ?
                    ORDER BY t.id DESC
                ";
                $stmt_tasks = $conn->prepare($sql_tasks);
                $stmt_tasks->bind_param("i", $user_id);
                $stmt_tasks->execute();
                $result_tasks = $stmt_tasks->get_result();

                if ($result_tasks->num_rows > 0):
                    while ($row = $result_tasks->fetch_assoc()):
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium"><?php echo $row['id']; ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($row['task_name']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars(ucfirst($row['assigned_to_name'])); ?></td>
                    </tr>
                <?php 
                    endwhile;
                else: 
                ?>
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada tugas dalam proyek Anda.</td></tr>
                <?php 
                endif;
                $stmt_tasks->close();
                ?>
            </tbody>
        </table>
    </div>
</div>