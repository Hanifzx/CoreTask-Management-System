<?php

global $conn;

// Total Tugas
$sql_total_tasks = "SELECT COUNT(id) AS total FROM tasks WHERE assigned_to = ?";
$stmt_total = $conn->prepare($sql_total_tasks);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_tasks = $stmt_total->get_result()->fetch_assoc()['total'];
$stmt_total->close();

// Status tugas: proses
$sql_progress_tasks = "SELECT COUNT(id) AS total FROM tasks WHERE assigned_to = ? AND status = 'proses'";
$stmt_progress = $conn->prepare($sql_progress_tasks);
$stmt_progress->bind_param("i", $user_id);
$stmt_progress->execute();
$progress_tasks = $stmt_progress->get_result()->fetch_assoc()['total'];
$stmt_progress->close();

// Status tugas: selesai
$sql_completed_tasks = "SELECT COUNT(id) AS total FROM tasks WHERE assigned_to = ? AND status = 'selesai'";
$stmt_completed = $conn->prepare($sql_completed_tasks);
$stmt_completed->bind_param("i", $user_id);
$stmt_completed->execute();
$completed_tasks = $stmt_completed->get_result()->fetch_assoc()['total'];
$stmt_completed->close();

?>

<div>
    <div class="h-18 px-8 py-12 mb-3 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Dashboard Team Member</h1>
        <p class="font-semibold text-sm text-gray-500">Berikut ringkasan tugas Anda</p>
    </div>

    <div id="card-container" class="flex gap-6 mx-8">
        <div class="w-1/3 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Total Tugas</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                class="h-5 w-5 fill-black"><path d="M133.8 36.3c10.9 7.6 13.5 22.6 5.9 33.4l-56 80c-4.1 5.8-10.5 9.5-17.6 10.1S52 158 47 153L7 113C-2.3 103.6-2.3 88.4 7 79S31.6 69.7 41 79l19.8 19.8 39.6-56.6c7.6-10.9 22.6-13.5 33.4-5.9zm0 160c10.9 7.6 13.5 22.6 5.9 33.4l-56 80c-4.1 5.8-10.5 9.5-17.6 10.1S52 318 47 313L7 273c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l19.8 19.8 39.6-56.6c7.6-10.9 22.6-13.5 33.4-5.9zM224 96c0-17.7 14.3-32 32-32l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32zm0 160c0-17.7 14.3-32 32-32l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32zM160 416c0-17.7 14.3-32 32-32l288 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-288 0c-17.7 0-32-14.3-32-32zM64 376a40 40 0 1 1 0 80 40 40 0 1 1 0-80z"/></svg>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $total_tasks; ?></h2>
        </div>
        <div class="w-1/3 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Tugas Diproses</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"
                class="h-5 w-5 fill-yellow-500"><path d="M32 0C14.3 0 0 14.3 0 32S14.3 64 32 64l0 11c0 42.4 16.9 83.1 46.9 113.1l67.9 67.9-67.9 67.9C48.9 353.9 32 394.6 32 437l0 11c-17.7 0-32 14.3-32 32s14.3 32 32 32l320 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-11c0-42.4-16.9-83.1-46.9-113.1l-67.9-67.9 67.9-67.9c30-30 46.9-70.7 46.9-113.1l0-11c17.7 0 32-14.3 32-32S369.7 0 352 0L32 0zM288 437l0 11-192 0 0-11c0-25.5 10.1-49.9 28.1-67.9l67.9-67.9 67.9 67.9c18 18 28.1 42.4 28.1 67.9z"/></svg>
            </div>
            <h2 class="font-bold text-3xl"><?php echo $progress_tasks; ?></h2>
        </div>
        <div class="w-1/3 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Tugas Selesai</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                class="h-6 w-6 fill-green-500"><path d="M256 512a256 256 0 1 1 0-512 256 256 0 1 1 0 512zm0-464a208 208 0 1 0 0 416 208 208 0 1 0 0-416zm70.7 121.9c7.8-10.7 22.8-13.1 33.5-5.3 10.7 7.8 13.1 22.8 5.3 33.5L243.4 366.1c-4.1 5.7-10.5 9.3-17.5 9.8-7 .5-13.9-2-18.8-6.9l-55.9-55.9c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l36 36 105.6-145.2z"/></svg>
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