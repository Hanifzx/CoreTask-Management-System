<?php
require_once '../src/config/connection.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}

exit();
?>