<?php
require_once '../src/config/connection.php';

global $conn;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Mengambil data jumlah project manager
$sql_total_pm = "SELECT COUNT(id) AS total FROM users WHERE role = 'project_manager'";
$result_pm = $conn->query($sql_total_pm);
$total_pm = $result_pm->fetch_assoc()['total'];

// Mengambil data jumlah team member
$sql_total_member = "SELECT COUNT(id) AS total FROM users WHERE role = 'team_member'";
$result_member = $conn->query($sql_total_member);
$total_member = $result_member->fetch_assoc()['total'];

// Mengambil data jumlah project
$sql_total_project = "SELECT COUNT(id) AS total FROM projects";
$result_project = $conn->query($sql_total_project);
$total_project = $result_project->fetch_assoc()['total'];

// Mengambil data project manager
$sql_pm_list = "
    SELECT 
        users.id, 
        users.username, 
        COUNT(projects.id) AS total_proyek
    FROM 
        users
    LEFT JOIN 
        projects ON users.id = projects.manager_id
    WHERE 
        users.role = 'project_manager'
    GROUP BY 
        users.id, users.username
    ORDER BY 
        total_proyek DESC
";
$result_pm_list = $conn->query($sql_pm_list);

// Mengambil data member
$sql_member_list = "
    SELECT 
        members.id, 
        members.username AS member_name, 
        COUNT(tasks.id) AS total_tugas,
        managers.username AS manager_name
    FROM 
        users AS members
    LEFT JOIN 
        tasks ON members.id = tasks.assigned_to
    LEFT JOIN 
        users AS managers ON members.project_manager_id = managers.id
    WHERE 
        members.role = 'team_member'
    GROUP BY 
        members.id, members.username, managers.username
    ORDER BY 
        total_tugas DESC
";

// Mengambil data project
$sql_project_list = "
    SELECT 
        projects.id, 
        projects.project_name, 
        projects.end_date,
        users.username AS manager_name
    FROM 
        projects
    LEFT JOIN 
        users ON projects.manager_id = users.id
    ORDER BY 
        projects.end_date ASC
";

$result_project_list = $conn->query($sql_project_list);

$result_member_list = $conn->query($sql_member_list);
?>

<div class="mx-2">
    <div class="h-18 px-8 py-13 mb-3 flex flex-col justify-center items-start">
        <h1 class="font-bold text-2xl">Dashboard Admin</h1>

        <?php if ($role == 'super_admin'): ?>
        <p class="font-semibold text-sm text-gray-500">Berikut ringkasan informasi proyek yang ada</p>
    </div>
    <div id="card-container" class="flex gap-6 mx-8">
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Total Project</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                class="h-5 w-5 fill-blue-900"><path d="M64 400l384 0c8.8 0 16-7.2 16-16l0-240c0-8.8-7.2-16-16-16l-149.3 0c-17.3 0-34.2-5.6-48-16L212.3 83.2c-2.8-2.1-6.1-3.2-9.6-3.2L64 80c-8.8 0-16 7.2-16 16l0 288c0 8.8 7.2 16 16 16zm384 48L64 448c-35.3 0-64-28.7-64-64L0 96C0 60.7 28.7 32 64 32l138.7 0c13.8 0 27.3 4.5 38.4 12.8l38.4 28.8c5.5 4.2 12.3 6.4 19.2 6.4L448 80c35.3 0 64 28.7 64 64l0 240c0 35.3-28.7 64-64 64z"/></svg>
            </div>
            <h2 class="font-bold text-3xl text-blue-900"><?php echo $total_project; ?></h2>
        </div>
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Total Project Manager</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                class="h-5 w-5 fill-blue-900"><path d="M512 80c8.8 0 16 7.2 16 16l0 320c0 8.8-7.2 16-16 16L64 432c-8.8 0-16-7.2-16-16L48 96c0-8.8 7.2-16 16-16l448 0zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM208 248a56 56 0 1 0 0-112 56 56 0 1 0 0 112zm-32 40c-44.2 0-80 35.8-80 80 0 8.8 7.2 16 16 16l192 0c8.8 0 16-7.2 16-16 0-44.2-35.8-80-80-80l-64 0zM376 144c-13.3 0-24 10.7-24 24s10.7 24 24 24l80 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-80 0zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24l80 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-80 0z"/></svg>
            </div>
            <h2 class="font-bold text-3xl text-blue-900"><?php echo $total_pm; ?></h2>
        </div>
        <div class="w-1/4 bg-white px-6 py-6 border-2 border-gray-200 rounded-2xl">
            <div class="mb-2 flex justify-between items-center">
                <h3 class="font-semibold text-md text-gray-400">Total Member</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                class="h-5 w-5 fill-blue-900"><path d="M64 128a112 112 0 1 1 224 0 112 112 0 1 1 -224 0zM0 464c0-97.2 78.8-176 176-176s176 78.8 176 176l0 6c0 23.2-18.8 42-42 42L42 512c-23.2 0-42-18.8-42-42l0-6zM432 64a96 96 0 1 1 0 192 96 96 0 1 1 0-192zm0 240c79.5 0 144 64.5 144 144l0 22.4c0 23-18.6 41.6-41.6 41.6l-144.8 0c6.6-12.5 10.4-26.8 10.4-42l0-6c0-51.5-17.4-98.9-46.5-136.7 22.6-14.7 49.6-23.3 78.5-23.3z"/></svg>
            </div>
            <h2 class="font-bold text-3xl text-blue-900"><?php echo $total_member; ?></h2>
        </div>
    </div>
    
    <div class="px-8 py-2 mt-4 flex flex-col justify-center items-start">
        <h1 class="font-bold text-lg">Daftar Project</h1>
    </div>
    <div class="overflow-x-auto mx-8 rounded-2xl border-2 border-gray-200">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-white text-gray-700 border-b border-gray-200 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Nama Proyek</th>
                    <th class="px-6 py-3 text-center">Tanggal Selesai</th>
                    <th class="px-6 py-3 text-left">Manajer Proyek</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                if ($result_project_list && $result_project_list->num_rows > 0):
                    while ($row = $result_project_list->fetch_assoc()):
                ?>
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-gray-800 font-medium"><?php echo $row['id']; ?></td>
                    <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars(ucfirst($row['project_name'])); ?></td>
                    <td class="px-6 py-4 text-center text-gray-700">
                        <?php 
                        if (!empty($row['end_date'])) {
                            $end_date = new DateTime($row['end_date']);
                            echo $end_date->format('d-m-Y'); 
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars(ucfirst($row['manager_name'] ?? 'N/A')); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data proyek.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-8 py-2 mt-4 flex flex-col justify-center items-start">
        <h1 class="font-bold text-lg">Daftar Project Manager</h1>
    </div>
    <div class="overflow-x-auto mx-8 rounded-2xl border-2 border-gray-200">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-white text-gray-700 border-b border-gray-200 uppercase font-semibold">
            <tr>
                <th class="px-6 py-3 text-left">ID</th>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-center">Proyek</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result_pm_list && $result_pm_list->num_rows > 0): ?>
                    <?php while ($row = $result_pm_list->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-800 font-medium"><?php echo $row['id']; ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars(ucfirst($row['username'])); ?></td>
                            <td class="px-6 py-4 text-center text-gray-700"><?php echo $row['total_proyek']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data project manager.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-8 py-2 mt-4 flex flex-col justify-center items-start">
        <h1 class="font-bold text-lg">Daftar Member</h1>
    </div>
    <div class="overflow-x-auto mx-8 mb-10 rounded-2xl border-2 border-gray-200">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-white text-gray-700 border-b border-gray-200 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Manajer Penanggung Jawab</th>
                    <th class="px-6 py-3 text-center">Total Tugas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result_member_list && $result_member_list->num_rows > 0): ?>
                    <?php while ($row = $result_member_list->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-800 font-medium">
                                <?php echo htmlspecialchars(ucfirst($row['member_name'])); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                <?php echo htmlspecialchars(ucfirst($row['manager_name'] ?? 'N/A')); ?>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-700">
                                <?php echo $row['total_tugas']; ?> Tugas
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data member.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
        <?php endif; ?>

<?php
// require_once '../src/partials/footer_tags.php';
?>