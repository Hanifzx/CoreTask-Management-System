<?php
// Memanggil file koneksi, yang juga otomatis memulai session
require_once '../src/config/connection.php';

// Memeriksa apakah session 'user_id' sudah ada (artinya sudah login)
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}

exit();
?>