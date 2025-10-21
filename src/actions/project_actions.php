<?php
require_once __DIR__ . '/../config/connection.php';

$manager_id = $_SESSION['user_id'];

// --- PROSES TAMBAH PROYEK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);
    if (empty($project_name)) {
        $_SESSION['flash_message'] = "Nama Proyek wajib diisi.";
        $_SESSION['flash_type'] = 'error';
    } else {
        $start_date = date('Y-m-d');
        $end_date = null;
        $sql_insert = "INSERT INTO projects (project_name, description, start_date, end_date, manager_id) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssi", $project_name, $description, $start_date, $end_date, $manager_id);
        if ($stmt_insert->execute()) {
            $_SESSION['flash_message'] = "Proyek '{$project_name}' berhasil ditambahkan.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Gagal menambahkan proyek: " . $conn->error;
            $_SESSION['flash_type'] = 'error';
        }
        $stmt_insert->close();
    }
    header("Location: ../../public/my_projects.php");
    exit();
}


// --- PROSES UPDATE PROYEK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    $edit_project_id = (int)$_POST['edit_project_id'];
    $edit_project_name = trim($_POST['edit_project_name']);
    $edit_description = trim($_POST['edit_description']);

    if (empty($edit_project_name)) {
        $_SESSION['flash_message'] = "Nama Proyek wajib diisi.";
        $_SESSION['flash_type'] = 'error';
    } else {
        // Verifikasi kepemilikan
        $sql_verify_update = "SELECT id FROM projects WHERE id = ? AND manager_id = ?";
        $stmt_verify_update = $conn->prepare($sql_verify_update);
        $stmt_verify_update->bind_param("ii", $edit_project_id, $manager_id);
        $stmt_verify_update->execute();
        $result_verify_update = $stmt_verify_update->get_result();
        $stmt_verify_update->close();

        if ($result_verify_update->num_rows === 1) {
            $sql_update = "UPDATE projects SET project_name = ?, description = ? WHERE id = ? AND manager_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssii", $edit_project_name, $edit_description, $edit_project_id, $manager_id);

            if ($stmt_update->execute()) {
                $_SESSION['flash_message'] = "Proyek '{$edit_project_name}' berhasil diperbarui.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Gagal memperbarui proyek: " . $conn->error;
                $_SESSION['flash_type'] = 'error';
            }
            $stmt_update->close();
        } else {
            $_SESSION['flash_message'] = "Gagal memperbarui: Proyek tidak ditemukan atau Anda tidak berhak.";
            $_SESSION['flash_type'] = 'error';
        }
    }
    header("Location: ../../public/my_projects.php");
    exit();
}


// --- PROSES HAPUS PROYEK ---
if (isset($_GET['delete'])) {
    $project_id_to_delete = (int)$_GET['delete'];
    $sql_verify = "SELECT id FROM projects WHERE id = ? AND manager_id = ?";
    $stmt_verify = $conn->prepare($sql_verify);
    $stmt_verify->bind_param("ii", $project_id_to_delete, $manager_id);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();
    $stmt_verify->close();
    if ($result_verify->num_rows === 1) {
        $sql_delete = "DELETE FROM projects WHERE id = ? AND manager_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $project_id_to_delete, $manager_id);
        if ($stmt_delete->execute()) {
            $_SESSION['flash_message'] = "Proyek berhasil dihapus.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Gagal menghapus proyek: " . $conn->error;
            $_SESSION['flash_type'] = 'error';
        }
        $stmt_delete->close();
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus: Proyek tidak ditemukan atau Anda tidak berhak.";
        $_SESSION['flash_type'] = 'error';
    }
    header("Location: ../../public/my_projects.php");
    exit();
}

header("Location: ../../public/my_projects.php");
exit();
