<?php
require_once '../src/config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/style.css">
    <title>Sidebar</title>
</head>
<body class="h-screen flex overflow-hidden">
        <div id="sidebar-container" class="w-56 flex flex-col bg-blue-950 text-white fixed top-0 left-0 h-screen">
            <div id="sidebar-navigation" class="flex flex-1 flex-col items-start mx-4 my-3">
                <h1 class="font-bold text-2xl mb-4">CoreTask</h1>
                <nav class="flex flex-col w-full text-sm">
                    <a href="dashboard.php" class="flex items-center block my-1 px-3 py-2 gap-2 rounded-xl hover:bg-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"
                        class="h-4 w-4 fill-white"><path d="M128 40c0-22.1-17.9-40-40-40L40 0C17.9 0 0 17.9 0 40L0 88c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48zm0 192c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48zM0 424l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40zM320 40c0-22.1-17.9-40-40-40L232 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48zM192 232l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40zM320 424c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40l48 0c22.1 0 40-17.9 40-40l0-48z"/></svg>
                        <h2>Dashboard</h2>
                    </a>
                    <!-- Pengecekan role yang login -->
                    <?php if ($role === 'super_admin'): ?>
                        <a href="manage_user.php" class="flex items-center block my-1 px-3 py-2 gap-2 rounded-xl hover:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                            class="h-4 w-4 fill-white"><path d="M48 416l0-256 480 0 0 256c0 8.8-7.2 16-16 16l-192 0c0-44.2-35.8-80-80-80l-64 0c-44.2 0-80 35.8-80 80l-32 0c-8.8 0-16-7.2-16-16zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM208 312a56 56 0 1 0 0-112 56 56 0 1 0 0 112zM376 208c-13.3 0-24 10.7-24 24s10.7 24 24 24l80 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-80 0zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24l80 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-80 0z"/></svg>
                            <h2>Data Pengguna</h2>
                        </a>
                        <a href="view_all_projects.php" class="flex items-center block my-1 px-3 py-2 gap-2 rounded-xl hover:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            class="h-4 w-4 fill-white"><path d="M64 400l384 0c8.8 0 16-7.2 16-16l0-240c0-8.8-7.2-16-16-16l-149.3 0c-17.3 0-34.2-5.6-48-16L212.3 83.2c-2.8-2.1-6.1-3.2-9.6-3.2L64 80c-8.8 0-16 7.2-16 16l0 288c0 8.8 7.2 16 16 16zm384 48L64 448c-35.3 0-64-28.7-64-64L0 96C0 60.7 28.7 32 64 32l138.7 0c13.8 0 27.3 4.5 38.4 12.8l38.4 28.8c5.5 4.2 12.3 6.4 19.2 6.4L448 80c35.3 0 64 28.7 64 64l0 240c0 35.3-28.7 64-64 64z"/></svg>
                            <h2>Semua Proyek</h2>
                        </a>
                    <?php elseif ($role === 'project_manager'): ?>
                        <a href="my_projects.php" class="flex items-center block my-1 px-3 py-2 gap-2 rounded-xl hover:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                            class="h-4 w-4 fill-white"><path d="M97.5 400l50-160 379.4 0-50 160-379.4 0zm190.7 48L477 448c21 0 39.6-13.6 45.8-33.7l50-160c9.7-30.9-13.4-62.3-45.8-62.3l-379.4 0c-21 0-39.6 13.6-45.8 33.7L80.2 294.4 80.2 96c0-8.8 7.2-16 16-16l138.7 0c3.5 0 6.8 1.1 9.6 3.2L282.9 112c13.8 10.4 30.7 16 48 16l117.3 0c8.8 0 16 7.2 16 16l48 0c0-35.3-28.7-64-64-64L330.9 80c-6.9 0-13.7-2.2-19.2-6.4L273.3 44.8C262.2 36.5 248.8 32 234.9 32L96.2 32c-35.3 0-64 28.7-64 64l0 288c0 35.3 28.7 64 64 64l192 0z"/></svg>
                            <h2>Proyek Saya</h2>
                        </a>
                        <a href="my_team.php" class="flex items-center block my-1 px-3 py-2 gap-2 rounded-xl hover:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            class="h-4 w-4 fill-white"><path d="M256 0a64 64 0 1 1 0 128 64 64 0 1 1 0-128zm96 312c0 25-12.7 47-32 59.9l0 92.1c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48l0-92.1C172.7 359 160 337 160 312l0-40c0-53 43-96 96-96s96 43 96 96l0 40zM96 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112zm16 240l0 32c0 32.5 12.1 62.1 32 84.7l0 75.3c0 1.2 0 2.5 .1 3.7-8.5 7.6-19.7 12.3-32.1 12.3l-32 0c-26.5 0-48-21.5-48-48l0-56.6C12.9 364.4 0 343.7 0 320l0-32c0-53 43-96 96-96 12.7 0 24.8 2.5 35.9 6.9-12.6 21.4-19.9 46.4-19.9 73.1zM368 464l0-75.3c19.9-22.5 32-52.2 32-84.7l0-32c0-26.7-7.3-51.6-19.9-73.1 11.1-4.5 23.2-6.9 35.9-6.9 53 0 96 43 96 96l0 32c0 23.7-12.9 44.4-32 55.4l0 56.6c0 26.5-21.5 48-48 48l-32 0c-12.3 0-23.6-4.6-32.1-12.3 0-1.2 .1-2.5 .1-3.7zM416 32a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                            <h2>Tim Saya</h2>
                        </a>
                    <?php elseif ($role === 'team_member'): ?>
                        <a href="my_tasks.php" class="flex items-center block my-1 px-3 py-2 gap-2 rounded-xl hover:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            class="h-4 w-4 fill-white"><path d="M133.8 36.3c10.9 7.6 13.5 22.6 5.9 33.4l-56 80c-4.1 5.8-10.5 9.5-17.6 10.1S52 158 47 153L7 113C-2.3 103.6-2.3 88.4 7 79S31.6 69.7 41 79l19.8 19.8 39.6-56.6c7.6-10.9 22.6-13.5 33.4-5.9zm0 160c10.9 7.6 13.5 22.6 5.9 33.4l-56 80c-4.1 5.8-10.5 9.5-17.6 10.1S52 318 47 313L7 273c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l19.8 19.8 39.6-56.6c7.6-10.9 22.6-13.5 33.4-5.9zM224 96c0-17.7 14.3-32 32-32l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32zm0 160c0-17.7 14.3-32 32-32l224 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-224 0c-17.7 0-32-14.3-32-32zM160 416c0-17.7 14.3-32 32-32l288 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-288 0c-17.7 0-32-14.3-32-32zM64 376a40 40 0 1 1 0 80 40 40 0 1 1 0-80z"/></svg>
                            <h2>Tugas Saya</h2>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
            <div class="flex flex-col mx-4 my-2">
                <div class="flex flex-row justify-start items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                    class="w-6 h-6 fill-white"><path d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l356.6 0c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3l-59.4 0z"/></svg>
                    <div>
                        <h1 class="font-semibold text-sm"><?php echo htmlspecialchars(ucfirst($username)); ?></h1>
                        <h1 class=" text-[13px] text-white-400">Role: <?php echo htmlspecialchars($role); ?></h1>
                    </div>
                </div>
                <div class="border mt-3"></div>
                <a href="logout.php" class="flex my-2 px-4 py-2 items-center justify-center gap-2 opacity-60 hover:opacity-100 rounded-xl hover:bg-blue-900">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                    class="h-4 w-4 fill-white"><path d="M160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0zM502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>
                    <p class="text-sm font-normal">Logout</p>
                </a>
            </div>
        </div>

        <main id="main-container" class="flex-1 ml-56 bg-white-blues overflow-y-auto h-screen" style="background-color: #e8e8e8ff">
        <!-- </main>
    </div>
</body>
</html> -->