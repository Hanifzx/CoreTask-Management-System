<?php
require_once __DIR__ . '/../config/connection.php';

// 1. Keamanan Awal: Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

// Ambil role dan id pengguna dari session
$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];


// =======================================================
// LOGIKA UNTUK PROJECT MANAGER
// =======================================================
if ($user_role === 'project_manager') {
    
    $manager_id = $user_id; // (Variabel ini digunakan di logika PM)

    // --- PROSES TAMBAH TUGAS (PM) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
        
        $project_id = (int)$_POST['task_project_id'];
        $task_name = trim($_POST['task_name']);
        $description = trim($_POST['task_description']);
        $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
        $status = 'belum'; // Status default sudah benar

        if (empty($project_id) || empty($task_name)) {
            $_SESSION['flash_message'] = "Nama Tugas dan Proyek wajib diisi.";
            $_SESSION['flash_type'] = 'error';
        } else {
            // Verifikasi PM memiliki proyek ini
            $sql_verify_project = "SELECT id FROM projects WHERE id = ? AND manager_id = ?";
            $stmt_verify = $conn->prepare($sql_verify_project);
            $stmt_verify->bind_param("ii", $project_id, $manager_id);
            $stmt_verify->execute();
            $result_verify = $stmt_verify->get_result();
            
            if ($result_verify->num_rows === 1) {
                // Jika ya, tambahkan tugas
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
        }
        
        header("Location: ../../public/my_projects.php");
        exit();
    }

    // --- PROSES UPDATE TUGAS (PM) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
        $edit_task_id = (int)$_POST['edit_task_id'];
        $edit_task_name = trim($_POST['edit_task_name']);
        $edit_assigned_to = !empty($_POST['edit_assigned_to']) ? (int)$_POST['edit_assigned_to'] : null;

        if (empty($edit_task_name)) {
            $_SESSION['flash_message'] = "Nama Tugas wajib diisi.";
            $_SESSION['flash_type'] = 'error';
        } else {
            // Verifikasi PM memiliki tugas ini (melalui proyek)
            $sql_verify_task_owner = "SELECT p.id FROM tasks t JOIN projects p ON t.project_id = p.id WHERE t.id = ? AND p.manager_id = ?";
            $stmt_verify_task = $conn->prepare($sql_verify_task_owner);
            $stmt_verify_task->bind_param("ii", $edit_task_id, $manager_id);
            $stmt_verify_task->execute();
            $result_verify_task = $stmt_verify_task->get_result();

            if ($result_verify_task->num_rows === 1) {
                // PM hanya bisa update nama dan penugasan, BUKAN STATUS
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

    // --- PROSES HAPUS TUGAS (PM) ---
    if (isset($_GET['delete_task'])) {
        $task_id_to_delete = (int)$_GET['delete_task'];

        // Verifikasi PM memiliki tugas ini
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
    
    // Default redirect untuk PM jika tidak ada aksi
    header("Location: ../../public/my_projects.php");
    exit();

// =======================================================
// LOGIKA UNTUK TEAM MEMBER
// =======================================================
} elseif ($user_role === 'team_member') {

    $member_id = $user_id; // (Variabel ini digunakan di logika TM)

    // --- PROSES UPDATE STATUS (TM) ---
    // Aksi ini dikirim dari form di 'my_tasks.php'
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    
        $task_id = (int)$_POST['task_id'];
        $new_status = $_POST['new_status'];

        // 1. Validasi status (pastikan nilainya sesuai yang diharapkan)
        $allowed_statuses = ['belum', 'proses', 'selesai'];
        if (!in_array($new_status, $allowed_statuses)) {
            $_SESSION['flash_message'] = "Status yang dipilih tidak valid.";
            $_SESSION['flash_type'] = 'error';
            
        } else {
            // 2. Verifikasi Kepemilikan (PENTING!)
            // Pastikan member ini hanya mengubah tugas yang memang ditugaskan padanya.
            $sql_verify = "SELECT id FROM tasks WHERE id = ? AND assigned_to = ?";
            $stmt_verify = $conn->prepare($sql_verify);
            $stmt_verify->bind_param("ii", $task_id, $member_id);
            $stmt_verify->execute();
            $result_verify = $stmt_verify->get_result();

            if ($result_verify->num_rows === 1) {
                // 3. Jika verifikasi berhasil, update tugasnya
                $sql_update = "UPDATE tasks SET status = ? WHERE id = ? AND assigned_to = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sii", $new_status, $task_id, $member_id);

                if ($stmt_update->execute()) {
                    $_SESSION['flash_message'] = "Status tugas berhasil diperbarui.";
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $_SESSION['flash_message'] = "Gagal memperbarui status: " . $conn->error;
                    $_SESSION['flash_type'] = 'error';
                }
                $stmt_update->close();
            } else {
                // Jika verifikasi gagal (mencoba update tugas orang lain)
                $_SESSION['flash_message'] = "Gagal: Anda tidak memiliki hak untuk mengubah tugas ini.";
                $_SESSION['flash_type'] = 'error';
            }
            $stmt_verify->close();
        }

        // Redirect kembali ke halaman tugas member
        header("Location: ../../public/my_tasks.php");
        exit();
    }

    // Default redirect untuk TM jika tidak ada aksi
    header("Location: ../../public/my_tasks.php");
    exit();

// =======================================================
// LOGIKA UNTUK ROLE LAIN (Misal: Super Admin)
// =======================================================
} else {
    // Jika role lain (spt Super Admin) mencoba mengakses file ini,
    // kembalikan mereka ke dashboard.
    $_SESSION['flash_message'] = "Aksi tidak diizinkan untuk peran Anda.";
    $_SESSION['flash_type'] = 'error';
    header("Location: ../../public/dashboard.php");
    exit();
}
?>