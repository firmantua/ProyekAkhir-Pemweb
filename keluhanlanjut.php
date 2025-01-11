<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "asramakita";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mendapatkan email dari sesi
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    // Query untuk mendapatkan NIM dari tabel mahasiswa berdasarkan email
    $query = "SELECT nim FROM mahasiswa WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nim = $data['nim'];
    } else {
        echo "<script>alert('Data mahasiswa tidak ditemukan!'); window.location.href='login.php';</script>";
        exit();
    }

    // Query keluhan berdasarkan NIM pengguna
    $query_keluhan = "SELECT id_keluhan, nama_lengkap, nim, kode_kamar, tanggal_keluhan, keluhan, status2 
                      FROM keluhan 
                      WHERE nim = '$nim' 
                      ORDER BY tanggal_keluhan DESC";
    $result_keluhan = mysqli_query($conn, $query_keluhan);
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kita Dorm - Keluhan Anda</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background-color: white; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .back-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            font-size: 16px;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            text-align: center;
        }
        .back-button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <h2>Daftar Keluhan Anda</h2>

    <!-- Tombol Back to Home -->
    <a href="home.php" class="back-button">Back to Home</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIM</th>
                <th>No Kamar</th>
                <th>Tanggal Keluhan</th>
                <th>Keluhan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if (mysqli_num_rows($result_keluhan) > 0) {
                while ($data = mysqli_fetch_assoc($result_keluhan)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($data['nama_lengkap']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['nim']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['kode_kamar']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['tanggal_keluhan']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['keluhan']) . "</td>";
                    echo "<td>" . htmlspecialchars($data['status2']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Belum ada data keluhan yang anda ajukan</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
