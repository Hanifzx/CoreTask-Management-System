<?php
require_once __DIR__ . '/../config/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'project_manager') {
    header('Location: ../../public/login.php');
    exit();
}

$manager_id = $_SESSION['user_id'];

// --- PROSES TAMBAH TUGAS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    
    $project_id = (int)$_POST['task_project_id'];
    $task_name = trim($_POST['task_name']);
    $description = trim($_POST['task_description']);
    $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
    $status = 'belum';

    if (empty($project_id) || empty($task_name)) {
        $_SESSION['flash_message'] = "Nama Tugas dan Proyek wajib diisi.";
        $_SESSION['flash_type'] = 'error';
        header("Location: ../../public/my_projects.php");
        exit();
    }

    $sql_verify_project = "SELECT id FROM projects WHERE id = ? AND manager_id = ?";
    $stmt_verify = $conn->prepare($sql_verify_project);
    $stmt_verify->bind_param("ii", $project_id, $manager_id);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();
    
    if ($result_verify->num_rows === 1) {
        $sql_insert = "INSERT INTO tasks (task_name, description, status, project_id, assigned_to) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssii", $task_name, $description, $status, $project_id, $assigned_to);

        if ($stmt_insert->execute()) {
            $_SESSION['flash_message'] = "Tugas '{$task_name}' berhasil ditambahkan.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Gagal menambahkan tugas: " . $conn->error;
            $_SESSION['flash_type'] = 'error';
        }
        $stmt_insert->close();
    } else {
        $_SESSION['flash_message'] = "Gagal: Anda tidak memiliki hak akses ke proyek tersebut.";
        $_SESSION['flash_type'] = 'error';
    }
    $stmt_verify->close();
    
    header("Location: ../../public/my_projects.php");
    exit();
}

// Jika ada aksi lain (Edit/Hapus Tugas) bisa ditambahkan di sini...

header("Location: ../../public/my_projects.php");
exit();
?>