<?php
require_once __DIR__ . '/../config/connection.php';

/*
|--------------------------------------------------------------------------
| File: task_actions.php
| Deskripsi:
| - File ini menangani semua aksi CRUD (Create, Update, Delete)
|   untuk tabel "tasks" tergantung pada peran pengguna (role).
| - Role yang diatur:
|   1. Project Manager → dapat menambah, mengedit, dan menghapus tugas.
|   2. Team Member → hanya dapat memperbarui status tugasnya sendiri.
|   3. Role lain → diarahkan kembali ke dashboard.
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

// Ambil data pengguna aktif dari session
$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

/* ----------------------------------------------------------------------
    LOGIKA UNTUK PROJECT MANAGER
---------------------------------------------------------------------- */
if ($user_role === 'project_manager') {
    
    $manager_id = $user_id; // ID manager untuk filter keamanan

    /* ===============================================================
        TAMBAH TUGAS
        ---------------------------------------------------------------
        - Aksi dikirim dari form tambah tugas di halaman my_projects.php
        - Hanya Project Manager dari proyek tersebut yang boleh menambah
        tugas ke proyek miliknya sendiri.
    =============================================================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
        
        $project_id = (int)$_POST['task_project_id'];
        $task_name = trim($_POST['task_name']);
        $description = trim($_POST['task_description']);
        $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
        $status = 'belum'; // Status default saat tugas baru ditambahkan

        if (empty($project_id) || empty($task_name)) {
            $_SESSION['flash_message'] = "Nama Tugas dan Proyek wajib diisi.";
            $_SESSION['flash_type'] = 'error';
        } else {
            // Verifikasi: pastikan proyek milik PM ini
            $sql_verify_project = "SELECT id FROM projects WHERE id = ? AND manager_id = ?";
            $stmt_verify = $conn->prepare($sql_verify_project);
            $stmt_verify->bind_param("ii", $project_id, $manager_id);
            $stmt_verify->execute();
            $result_verify = $stmt_verify->get_result();
            
            if ($result_verify->num_rows === 1) {
                // Tambahkan tugas baru ke proyek
                $sql_insert = "INSERT INTO tasks (task_name, description, status, project_id, assigned_to)
                                VALUES (?, ?, ?, ?, ?)";
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

    /* ===============================================================
        UPDATE TUGAS
        ---------------------------------------------------------------
        - Hanya Project Manager yang punya proyek terkait
            yang boleh memperbarui nama tugas atau orang yang ditugaskan.
        - Status tugas tidak bisa diubah oleh PM (hanya oleh anggota).
    =============================================================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
        $edit_task_id = (int)$_POST['edit_task_id'];
        $edit_task_name = trim($_POST['edit_task_name']);
        $edit_assigned_to = !empty($_POST['edit_assigned_to']) ? (int)$_POST['edit_assigned_to'] : null;

        if (empty($edit_task_name)) {
            $_SESSION['flash_message'] = "Nama Tugas wajib diisi.";
            $_SESSION['flash_type'] = 'error';
        } else {
            // Verifikasi bahwa tugas ini memang dari proyek milik PM
            $sql_verify_task_owner = "
                SELECT p.id
                FROM tasks t
                JOIN projects p ON t.project_id = p.id
                WHERE t.id = ? AND p.manager_id = ?
            ";
            $stmt_verify_task = $conn->prepare($sql_verify_task_owner);
            $stmt_verify_task->bind_param("ii", $edit_task_id, $manager_id);
            $stmt_verify_task->execute();
            $result_verify_task = $stmt_verify_task->get_result();

            if ($result_verify_task->num_rows === 1) {
                // Perbarui data tugas
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

    /* ===============================================================
        HAPUS TUGAS
        ---------------------------------------------------------------
        - Hanya Project Manager yang memiliki proyek
            tempat tugas itu berada yang boleh menghapusnya.
    =============================================================== */
    if (isset($_GET['delete_task'])) {
        $task_id_to_delete = (int)$_GET['delete_task'];

        // Verifikasi kepemilikan proyek sebelum menghapus
        $sql_verify_delete_owner = "
            SELECT p.id
            FROM tasks t
            JOIN projects p ON t.project_id = p.id
            WHERE t.id = ? AND p.manager_id = ?
        ";
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

    // Jika tidak ada aksi, kembalikan ke halaman proyek PM
    header("Location: ../../public/my_projects.php");
    exit();


/* ----------------------------------------------------------------------
    LOGIKA UNTUK TEAM MEMBER
---------------------------------------------------------------------- */
} elseif ($user_role === 'team_member') {

    $member_id = $user_id; // ID member aktif

    /* ===============================================================
        UPDATE STATUS TUGAS
        ---------------------------------------------------------------
        - Hanya Team Member yang ditugaskan ke task tersebut
            yang boleh mengubah status (belum/proses/selesai).
    =============================================================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    
        $task_id = (int)$_POST['task_id'];
        $new_status = $_POST['new_status'];

        // Validasi nilai status (tidak boleh bebas)
        $allowed_statuses = ['belum', 'proses', 'selesai'];
        if (!in_array($new_status, $allowed_statuses)) {
            $_SESSION['flash_message'] = "Status yang dipilih tidak valid.";
            $_SESSION['flash_type'] = 'error';
        } else {
            // Verifikasi bahwa tugas ini memang milik member tersebut
            $sql_verify = "SELECT id FROM tasks WHERE id = ? AND assigned_to = ?";
            $stmt_verify = $conn->prepare($sql_verify);
            $stmt_verify->bind_param("ii", $task_id, $member_id);
            $stmt_verify->execute();
            $result_verify = $stmt_verify->get_result();

            if ($result_verify->num_rows === 1) {
                // Update status tugas
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
                $_SESSION['flash_message'] = "Gagal: Anda tidak memiliki hak untuk mengubah tugas ini.";
                $_SESSION['flash_type'] = 'error';
            }
            $stmt_verify->close();
        }

        header("Location: ../../public/my_tasks.php");
        exit();
    }

    // Jika tidak ada aksi, arahkan kembali ke halaman tugas member
    header("Location: ../../public/my_tasks.php");
    exit();

/* ----------------------------------------------------------------------
    LOGIKA UNTUK ROLE LAIN
---------------------------------------------------------------------- */
} else {
    // Jika bukan PM atau TM, tolak akses dan kembali ke dashboard
    $_SESSION['flash_message'] = "Aksi tidak diizinkan untuk peran Anda.";
    $_SESSION['flash_type'] = 'error';
    header("Location: ../../public/dashboard.php");
    exit();
}
?>
