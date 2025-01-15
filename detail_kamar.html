<?php
include 'koneksi.php';
session_start();

// Aktifkan pelaporan kesalahan untuk debugging (nonaktifkan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan user sudah login dan memiliki 'nim'
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
    exit();
}

// Mendapatkan email dari sesi
$email = $_SESSION['user_email'];

// Mengambil nim dari tabel mahasiswa berdasarkan email
$stmt_mahasiswa = $conn->prepare("SELECT nim FROM mahasiswa WHERE email = ?");
if (!$stmt_mahasiswa) {
    die("Prepare failed: " . $conn->error);
}
$stmt_mahasiswa->bind_param("s", $email);
$stmt_mahasiswa->execute();
$result_mahasiswa = $stmt_mahasiswa->get_result();

if ($result_mahasiswa->num_rows > 0) {
    $data_mahasiswa = $result_mahasiswa->fetch_assoc();
    $nim = htmlspecialchars($data_mahasiswa['nim'], ENT_QUOTES, 'UTF-8');
} else {
    echo "<script>alert('Data mahasiswa tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt_mahasiswa->close();

// Ambil kode kamar dari parameter URL
$kode_kamar = isset($_GET['kode']) ? $_GET['kode'] : '';

// Validasi kode kamar
if (empty($kode_kamar)) {
    die("Kode kamar tidak diberikan.");
}

// Mengambil data kamar berdasarkan kode kamar dengan prepared statements
$stmt_kamar = $conn->prepare("SELECT * FROM kamar WHERE kode_kamar = ?");
if (!$stmt_kamar) {
    die("Prepare failed: " . $conn->error);
}
$stmt_kamar->bind_param("s", $kode_kamar);
$stmt_kamar->execute();
$result_kamar = $stmt_kamar->get_result();

if ($result_kamar->num_rows == 0) {
    die("Kamar tidak ditemukan.");
}

$row = $result_kamar->fetch_assoc();
$stmt_kamar->close();

// Jika tombol Kirim ditekan, update bagian "terisi" dan insert ke mahasiswa
if (isset($_POST['kirim'])) {
    // Pastikan kapasitas belum penuh
    if ($row['terisi'] < $row['kapasitas']) {
        $terisi_baru = $row['terisi'] + 1;

        // Mulai transaksi
        $conn->begin_transaction();

        try {
            // Update jumlah "terisi" di tabel kamar menggunakan prepared statements
            $stmt_update_kamar = $conn->prepare("UPDATE kamar SET terisi = ? WHERE kode_kamar = ?");
            if (!$stmt_update_kamar) {
                throw new Exception("Prepare failed untuk update kamar: " . $conn->error);
            }
            $stmt_update_kamar->bind_param("is", $terisi_baru, $kode_kamar);
            if (!$stmt_update_kamar->execute()) {
                throw new Exception("Execute failed untuk update kamar: " . $stmt_update_kamar->error);
            }
            $stmt_update_kamar->close();

            // Update kode_kamar di tabel mahasiswa menggunakan prepared statements
            $stmt_update_mahasiswa = $conn->prepare("UPDATE mahasiswa SET kode_kamar = ? WHERE nim = ?");
            if (!$stmt_update_mahasiswa) {
                throw new Exception("Prepare failed untuk update mahasiswa: " . $conn->error);
            }
            $stmt_update_mahasiswa->bind_param("ss", $kode_kamar, $nim);
            if (!$stmt_update_mahasiswa->execute()) {
                throw new Exception("Execute failed untuk update mahasiswa: " . $stmt_update_mahasiswa->error);
            }
            $stmt_update_mahasiswa->close();

            // Commit transaksi
            $conn->commit();

            // Redirect ke pembayaran.php
            header("Location: pembayaran.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Gagal memperbarui data: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Kamar sudah penuh!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kamar</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styling sederhana untuk tampilan */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: left;
        }
        .content {
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }
        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .buttons {
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
    <a href="home.php" class="logo">
            <i class="fa-solid fa-house-user"></i> Asrama Kita
        </a>
    </div>

    <div class="content">
        <h2>Kamar <?= htmlspecialchars($row['kode_kamar']); ?></h2>
        <table>
            <tr>
                <th>Kode</th>
                <th>Kapasitas</th>
                <th>Terisi</th>
            </tr>
            <tr>
                <td><strong><?= htmlspecialchars($row['kode_kamar']); ?></strong></td>
                <td><?= $row['kapasitas']; ?></td>
                <td><?= $row['terisi']; ?>/<?= $row['kapasitas']; ?></td>
            </tr>
        </table>
        <div class="buttons">
            <a href="kamar.php" class="btn">Kembali</a>
            <form method="POST" style="display: inline;">
                <button type="submit" name="kirim" class="btn">Kirim</button>
            </form>
        </div>
    </div>
</body>
</html>
