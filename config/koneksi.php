<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pengaduan_db"; // Pastikan nama databasemu sama di phpMyAdmin

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Waduh, koneksi gagal nih: " . mysqli_connect_error());
}
?>