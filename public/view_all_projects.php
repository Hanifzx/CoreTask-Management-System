<?php
require_once '../src/config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../src/partials/sidebar.php';
?>

<h1>ini adalah halaman untuk atmin melihat semua proyek membernya</h1>

<?php
require_once '../src/partials/footer_tags.php';
?>