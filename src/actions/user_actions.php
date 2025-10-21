<?php
// Selalu panggil koneksi di file aksi juga
require_once __DIR__ . '/../config/connection.php';

// Pastikan hanya Super Admin yang bisa menjalankan aksi ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../../public/dashboard.php');
    exit();
}

// ===================================
// UTAMAKAN AKSI POST (DARI FORM)
// ===================================

// PROSES TAMBAH PENGGUNA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_user'])) {
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password'];
    $new_role = $_POST['role'];
    $new_manager_id = ($new_role === 'team_member' && !empty($_POST['project_manager_id'])) ? (int)$_POST['project_manager_id'] : null;

    if (empty($new_username) || empty($new_password) || empty($new_role) || ($new_role === 'team_member' && $new_manager_id === null)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi, dan Team Member harus punya Manajer!";
        $_SESSION['flash_type'] = 'error';
    } else {
        $sql_check = "SELECT id FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $new_username);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $_SESSION['flash_message'] = "Username '{$new_username}' sudah digunakan!";
            $_SESSION['flash_type'] = 'error';
        } else {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO users (username, password, role, project_manager_id) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sssi", $new_username, $password_hash, $new_role, $new_manager_id);
            if ($stmt_insert->execute()) {
                $_SESSION['flash_message'] = "Pengguna '{$new_username}' berhasil ditambahkan.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Gagal menambahkan pengguna: " . $conn->error;
                $_SESSION['flash_type'] = 'error';
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    header("Location: ../../public/manage_user.php");
    exit();
}

// PROSES UPDATE PENGGUNA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $edit_id = (int)$_POST['edit_user_id'];
    $edit_username = trim($_POST['edit_username']);
    $edit_password = $_POST['edit_password'];
    $edit_role = $_POST['edit_role'];
    $edit_manager_id = ($edit_role === 'team_member' && !empty($_POST['edit_project_manager_id'])) ? (int)$_POST['edit_project_manager_id'] : null;

    if (empty($edit_username) || empty($edit_role) || ($edit_role === 'team_member' && $edit_manager_id === null)) {
        $_SESSION['flash_message'] = "Username, Role wajib diisi, dan Team Member harus punya Manajer!";
        $_SESSION['flash_type'] = 'error';
    } else {
        $sql_check_edit = "SELECT id FROM users WHERE username = ? AND id != ?";
        $stmt_check_edit = $conn->prepare($sql_check_edit);
        $stmt_check_edit->bind_param("si", $edit_username, $edit_id);
        $stmt_check_edit->execute();
        if ($stmt_check_edit->get_result()->num_rows > 0) {
            $_SESSION['flash_message'] = "Username '{$edit_username}' sudah digunakan!";
            $_SESSION['flash_type'] = 'error';
        } else {
            $sql_update = "UPDATE users SET username = ?, role = ?, project_manager_id = ?";
            $params = [$edit_username, $edit_role, $edit_manager_id];
            $types = "ssi";

            if (!empty($edit_password)) {
                $password_hash_edit = password_hash($edit_password, PASSWORD_DEFAULT);
                $sql_update .= ", password = ?";
                $params[] = $password_hash_edit;
                $types .= "s";
            }
            $sql_update .= " WHERE id = ?";
            $params[] = $edit_id;
            $types .= "i";

            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param($types, ...$params);
            if ($stmt_update->execute()) {
                $_SESSION['flash_message'] = "Data pengguna '{$edit_username}' berhasil diperbarui.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Gagal memperbarui pengguna: " . $conn->error;
                $_SESSION['flash_type'] = 'error';
            }
            $stmt_update->close();
        }
        $stmt_check_edit->close();
    }
    header("Location: ../../public/manage_user.php");
    exit();
}

// ===================================
// TANGANI AKSI GET (DARI LINK)
// ===================================

// PROSES HAPUS PENGGUNA (DARI manage_user.php)
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql_delete = "DELETE FROM users WHERE id = ? AND role != 'super_admin'";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        $_SESSION['flash_message'] = ($stmt_delete->affected_rows > 0) ? "Pengguna berhasil dihapus." : "Gagal menghapus (ID tidak ditemukan atau Super Admin).";
        $_SESSION['flash_type'] = ($stmt_delete->affected_rows > 0) ? 'success' : 'error';
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus pengguna: " . $conn->error;
        $_SESSION['flash_type'] = 'error';
    }
    $stmt_delete->close();
    header("Location: ../../public/manage_user.php");
    exit();
}

// PROSES HAPUS PROYEK (DARI view_all_projects.php)
if (isset($_GET['delete_project'])) {
    $project_id = (int)$_GET['delete_project'];

    // Keamanan: Hapus dulu semua tasks yang terkait dengan proyek ini
    $sql_delete_tasks = "DELETE FROM tasks WHERE project_id = ?";
    $stmt_tasks = $conn->prepare($sql_delete_tasks);
    $stmt_tasks->bind_param("i", $project_id);
    $stmt_tasks->execute();
    $stmt_tasks->close();

    // Setelah tasks bersih, baru hapus proyeknya
    $sql_delete_project = "DELETE FROM projects WHERE id = ?";
    $stmt_project = $conn->prepare($sql_delete_project);
    $stmt_project->bind_param("i", $project_id);

    if ($stmt_project->execute()) {
        $_SESSION['flash_message'] = "Proyek berhasil dihapus (beserta semua tugas di dalamnya).";
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus proyek: " . $conn->error;
        $_SESSION['flash_type'] = 'error';
    }
    $stmt_project->close();
    
    header("Location: ../../public/view_all_projects.php");
    exit();
}

// ===================================
// JIKA TIDAK ADA AKSI DI ATAS
// ===================================

// Jika tidak ada aksi yang cocok, redirect ke halaman utama admin
header("Location: ../../public/manage_user.php");
exit();