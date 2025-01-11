<?php
$host = "localhost"; // Server database
$user = "root";      // Username MySQL
$pass = "";          // Password MySQL
$db   = "asramakita"; // Nama database

// Koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
