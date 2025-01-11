<?php
// Koneksi ke database
$host = "localhost"; // Server database
$user = "root";      // Username MySQL (default root)
$pass = "";          // Password MySQL (kosong di XAMPP)
$db   = "asramakita"; // Nama database

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Jika tombol aksi ditekan, perbarui status2
if (isset($_POST['update_status2'])) {
    $id_keluhan = $_POST['id_keluhan'];
    $status2 = $_POST['update_status2']; // Nilai status2 (Belum, Proses, Selesai, Ditolak)
    
    // Query untuk memperbarui status2 keluhan
    $query_update = "UPDATE keluhan SET status2 = '$status2' WHERE id_keluhan = $id_keluhan";
    
    // Eksekusi query dan cek apakah berhasil
    if (mysqli_query($koneksi, $query_update)) {
        // Jika berhasil, redirect ke halaman yang sama agar status2 terbaru terlihat
        header("Location: keluhanadmin.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi); // Menampilkan error jika query gagal
    }
}

// Ambil data keluhan dari database
$query = "SELECT * FROM keluhan ORDER BY tanggal_keluhan DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kita Dorm - Keluhan Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        button { padding: 5px 10px; cursor: pointer; border: none; border-radius: 4px; font-weight: bold; }
        .btn { background-color: #3498db; color: white; }
        .btn:hover { background-color: #2980b9; }
        .actions .back {
            background-color: #ddd;
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Daftar Keluhan Mahasiswa - Admin</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIM</th>
                <th>No Kamar</th>
                <th>Tanggal Keluhan</th>
                <th>Keluhan</th>
                <th>status2</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($data = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($data['nama_lengkap']) . "</td>";
                echo "<td>" . htmlspecialchars($data['nim']) . "</td>";
                echo "<td>" . htmlspecialchars($data['kode_kamar']) . "</td>";
                echo "<td>" . htmlspecialchars($data['tanggal_keluhan']) . "</td>";
                echo "<td>" . htmlspecialchars($data['keluhan']) . "</td>";
                echo "<td>" . htmlspecialchars($data['status2']) . "</td>";
                echo "<td>";

                // Dropdown untuk memilih status2
                echo "<form method='POST' style='display:inline;'>
                        <input type='hidden' name='id_keluhan' value='" . $data['id_keluhan'] . "'>
                        <select name='update_status2' class='btn'>
                            <option value='Belum' " . ($data['status2'] == 'Belum' ? 'selected' : '') . ">Belum</option>
                            <option value='Proses' " . ($data['status2'] == 'Proses' ? 'selected' : '') . ">Proses</option>
                            <option value='Selesai' " . ($data['status2'] == 'Selesai' ? 'selected' : '') . ">Selesai</option>
                            <option value='Ditolak' " . ($data['status2'] == 'Ditolak' ? 'selected' : '') . ">Ditolak</option>
                        </select>
                        <button type='submit' class='btn'>Perbarui status</button>
                      </form>";

                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="actions">
        <button type="button" class="back" onclick="history.back()">Kembali</button>
            </div>
</body>
</html>
