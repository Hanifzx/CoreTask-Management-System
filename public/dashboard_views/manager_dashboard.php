<?php

global $conn;

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
        <p class="font-semibold text-sm text-gray-500">Berikut ringkasan proyek dan tim Anda</p>
    </div>

    <div id="card-container" class="flex gap-6 mx-8">
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Proyek Anda</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                class="h-5 w-5 fill-black"><path d="M97.5 400l50-160 379.4 0-50 160-379.4 0zm190.7 48L477 448c21 0 39.6-13.6 45.8-33.7l50-160c9.7-30.9-13.4-62.3-45.8-62.3l-379.4 0c-21 0-39.6 13.6-45.8 33.7L80.2 294.4 80.2 96c0-8.8 7.2-16 16-16l138.7 0c3.5 0 6.8 1.1 9.6 3.2L282.9 112c13.8 10.4 30.7 16 48 16l117.3 0c8.8 0 16 7.2 16 16l48 0c0-35.3-28.7-64-64-64L330.9 80c-6.9 0-13.7-2.2-19.2-6.4L273.3 44.8C262.2 36.5 248.8 32 234.9 32L96.2 32c-35.3 0-64 28.7-64 64l0 288c0 35.3 28.7 64 64 64l192 0z"/></svg>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $total_my_projects; ?></h2>
        </div>
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Anggota Tim Anda</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                class="h-5 w-5 fill-black"><path d="M256 0a64 64 0 1 1 0 128 64 64 0 1 1 0-128zm96 312c0 25-12.7 47-32 59.9l0 92.1c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48l0-92.1C172.7 359 160 337 160 312l0-40c0-53 43-96 96-96s96 43 96 96l0 40zM96 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112zm16 240l0 32c0 32.5 12.1 62.1 32 84.7l0 75.3c0 1.2 0 2.5 .1 3.7-8.5 7.6-19.7 12.3-32.1 12.3l-32 0c-26.5 0-48-21.5-48-48l0-56.6C12.9 364.4 0 343.7 0 320l0-32c0-53 43-96 96-96 12.7 0 24.8 2.5 35.9 6.9-12.6 21.4-19.9 46.4-19.9 73.1zM368 464l0-75.3c19.9-22.5 32-52.2 32-84.7l0-32c0-26.7-7.3-51.6-19.9-73.1 11.1-4.5 23.2-6.9 35.9-6.9 53 0 96 43 96 96l0 32c0 23.7-12.9 44.4-32 55.4l0 56.6c0 26.5-21.5 48-48 48l-32 0c-12.3 0-23.6-4.6-32.1-12.3 0-1.2 .1-2.5 .1-3.7zM416 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $total_my_team; ?></h2>
        </div>
    </div>

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
                        <td class="px-6 py-4">
                            <?php
                            if ($row['status'] == 'selesai') {
                                echo '<span class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Selesai</span>';
                            } elseif ($row['status'] == 'proses') {
                                echo '<span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 ring-1 ring-inset ring-blue-600/20">Proses</span>';
                            } else {
                                echo '<span class="inline-flex items-center rounded-md bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Belum</span>';
                            }
                            ?>
                        </td>
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