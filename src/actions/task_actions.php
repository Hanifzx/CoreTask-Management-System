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

// --- PROSES UPDATE TUGAS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $edit_task_id = (int)$_POST['edit_task_id'];
    $edit_task_name = trim($_POST['edit_task_name']);
    $edit_assigned_to = !empty($_POST['edit_assigned_to']) ? (int)$_POST['edit_assigned_to'] : null;
    $edit_project_id_for_task = (int)$_POST['edit_project_id_for_task']; // Ambil ID Proyek dari input hidden

    if (empty($edit_task_name)) {
        $_SESSION['flash_message'] = "Nama Tugas wajib diisi.";
        $_SESSION['flash_type'] = 'error';
    } else {
        $sql_verify_task_owner = "SELECT p.id FROM tasks t JOIN projects p ON t.project_id = p.id WHERE t.id = ? AND p.manager_id = ?";
        $stmt_verify_task = $conn->prepare($sql_verify_task_owner);
        $stmt_verify_task->bind_param("ii", $edit_task_id, $manager_id);
        $stmt_verify_task->execute();
        $result_verify_task = $stmt_verify_task->get_result();

        if ($result_verify_task->num_rows === 1) {
            $sql_update = "UPDATE tasks SET task_name = ?, assigned_to = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sii", $edit_task_name, $edit_assigned_to, $edit_task_id);

            if ($stmt_update->execute()) {
                $_SESSION['flash_message'] = "Tugas '{$edit_task_name}' berhasil diperbarui.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Gagal memperbarui tugas: " . $conn->error;
                $_SESSION['flash_type'] = 'error';
            }
            $stmt_update->close();
        } else {
            $_SESSION['flash_message'] = "Gagal: Tugas tidak ditemukan atau Anda tidak berhak mengeditnya.";
            $_SESSION['flash_type'] = 'error';
        }
        $stmt_verify_task->close();
    }
    header("Location: ../../public/my_projects.php"); 
    exit();
}


// --- PROSES HAPUS TUGAS ---
if (isset($_GET['delete_task'])) {
    $task_id_to_delete = (int)$_GET['delete_task'];

    $sql_verify_delete_owner = "SELECT p.id FROM tasks t JOIN projects p ON t.project_id = p.id WHERE t.id = ? AND p.manager_id = ?";
    $stmt_verify_delete = $conn->prepare($sql_verify_delete_owner);
    $stmt_verify_delete->bind_param("ii", $task_id_to_delete, $manager_id);
    $stmt_verify_delete->execute();
    $result_verify_delete = $stmt_verify_delete->get_result();

    if ($result_verify_delete->num_rows === 1) {
        $sql_delete = "DELETE FROM tasks WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $task_id_to_delete);

        if ($stmt_delete->execute()) {
            $_SESSION['flash_message'] = "Tugas berhasil dihapus.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Gagal menghapus tugas: " . $conn->error;
            $_SESSION['flash_type'] = 'error';
        }
        $stmt_delete->close();
    } else {
        $_SESSION['flash_message'] = "Gagal: Tugas tidak ditemukan atau Anda tidak berhak menghapusnya.";
        $_SESSION['flash_type'] = 'error';
    }
    $stmt_verify_delete->close();

    header("Location: ../../public/my_projects.php");
    exit();
}

header("Location: ../../public/my_projects.php");
exit();
?>