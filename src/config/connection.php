<?php
session_start();

define ('DB_HOST', 'localhost');
define ('DB_USER', 'root');
define ('DB_PASS', '');
define ('DB_NAME', 'db_coretask');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("koneksi Gagal: " . $conn->connect_error);
}
?>