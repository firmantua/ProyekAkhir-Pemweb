<?php
session_start(); // Mulai session

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Contoh data user dari session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Persetujuan Admin</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img style="height: 100px; width:100px" src="asem.png" alt="Logo Kita Dorm">
            </div>
        </header>
        <main>
            <div class="message">
                <h2>Menunggu Persetujuan Admin</h2>
                <p>Admin asrama akan meninjau pembayaran Anda dan melakukan verifikasi. Proses ini biasanya memerlukan waktu 1x24 jam. Harap cek notifikasi Anda secara berkala untuk menerima update status pembayaran.</p>
            </div>
            <!-- Tombol kembali dengan link ke halaman mahasiswa -->
            <a href="index_mahasiswa.php" class="btn-back">Kembali</a>
        </main>
    </div>
</body>
</html>
