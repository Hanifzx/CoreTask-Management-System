<?php
// Memanggil file connection untuk memulai session
require_once '../src/config/connection.php';

session_destroy();

header('Location: login.php');
exit();
?>