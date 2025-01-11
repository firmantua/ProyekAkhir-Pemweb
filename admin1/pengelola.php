<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'asramakita';

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses form jika dikirim (Tambah data)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $telepon = $_POST['telepon'];
    $jabatan = $_POST['jabatan'];
    $status = $_POST['status'];

    // Proses upload foto
    $foto_name = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto_name = time() . "_" . basename($_FILES['foto']['name']);
        $target_dir = "admin/image/";
        $target_file = $target_dir . $foto_name;

        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Pindahkan file foto ke folder target
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            // Simpan path file foto ke database
            $foto_name = $target_file;
        } else {
            echo "<p style='color:red'>Gagal mengupload foto.</p>";
        }
    }

    // Simpan data ke database
    $sql = "INSERT INTO pengelola_asrama (nama_lengkap, nomor_telepon, jabatan, status, foto_profil)
            VALUES ('$nama', '$telepon', '$jabatan', '$status', '$foto_name')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>Data berhasil ditambahkan!</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Proses form edit data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $telepon = $_POST['telepon'];
    $jabatan = $_POST['jabatan'];
    $status = $_POST['status'];

    // Proses foto jika diunggah
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = time() . "_" . basename($_FILES['foto']['name']);
        $target_dir = "admin/image/";
        $target_file = $target_dir . $foto_name;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            // Update dengan foto baru
            $sql = "UPDATE pengelola_asrama 
                    SET nama_lengkap='$nama', nomor_telepon='$telepon', jabatan='$jabatan', status='$status', foto_profil='$target_file' 
                    WHERE id=$id";
        } else {
            echo "<p style='color:red'>Gagal mengupload foto.</p>";
        }
    } else {
        // Update tanpa mengubah foto
        $sql = "UPDATE pengelola_asrama 
                SET nama_lengkap='$nama', nomor_telepon='$telepon', jabatan='$jabatan', status='$status' 
                WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>Data berhasil diperbarui!</p>";
        header("Location: pengelola.php"); // Redirect agar form tidak dikirim ulang
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Proses Hapus Data
if (isset($_GET['hapus_id'])) {
    $hapus_id = $_GET['hapus_id'];

    // Ambil data foto untuk dihapus
    $sql = "SELECT foto_profil FROM pengelola_asrama WHERE id = $hapus_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        // Hapus file foto dari server
        if (file_exists($data['foto_profil'])) {
            unlink($data['foto_profil']);
        }
    }

    // Hapus data dari database
    $sql = "DELETE FROM pengelola_asrama WHERE id = $hapus_id";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green'>Data berhasil dihapus!</p>";
        header("Location: pengelola.php"); // Redirect agar form tidak dikirim ulang
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Query untuk mendapatkan data karyawan
$sql = "SELECT * FROM pengelola_asrama";
$result = $conn->query($sql);

// Jika tombol Edit diklik, ambil data lama
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = "SELECT * FROM pengelola_asrama WHERE id = $edit_id";
    $edit_result = $conn->query($edit_query);

    if ($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Pengelola Asrama</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { margin: 20px auto; max-width: 1200px; text-align: center; }
        h1, h2 { margin-bottom: 20px; }
        form { margin-bottom: 40px; }
        form input, form select { margin: 5px 0; padding: 8px; width: 300px; }
        form button { padding: 10px 15px; cursor: pointer; }
        .card-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .card { width: 250px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center; }
        .card img { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 10px; }
        .status { font-weight: bold; }
        .status.aktif { color: green; }
        .status.tidak-aktif { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Biodata Pengelola Asrama</h1>

        <!-- Form Tambah Data -->
        <h2>Tambah Pengelola Asrama</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="nama" placeholder="Nama Lengkap" required><br>
            <input type="text" name="telepon" placeholder="Nomor Telepon" required><br>
            <input type="text" name="jabatan" placeholder="Jabatan" required><br>
            <select name="status" required>
                <option value="Aktif">Aktif</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select><br>
            <input type="file" name="foto" accept="image/*" required><br><br>
            <button type="submit" name="tambah">Tambah Data</button>
        </form>

        <!-- Form Edit Data (Jika ada id edit) -->
        <?php if (isset($edit_data)): ?>
            <h2>Edit Pengelola Asrama</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">

                <input type="text" name="nama" value="<?php echo htmlspecialchars($edit_data['nama_lengkap']); ?>" required><br>
                <input type="text" name="telepon" value="<?php echo htmlspecialchars($edit_data['nomor_telepon']); ?>" required><br>
                <input type="text" name="jabatan" value="<?php echo htmlspecialchars($edit_data['jabatan']); ?>" required><br>
                <select name="status" required>
                    <option value="Aktif" <?php echo ($edit_data['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="Tidak Aktif" <?php echo ($edit_data['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select><br>
                <label>Ganti Foto:</label>
                <input type="file" name="foto" accept="image/*"><br><br>
                <button type="submit" name="update">Update Data</button>
            </form>
        <?php endif; ?>

        <!-- Menampilkan Data Pengelola Asrama -->
        <h2>Data Pengelola Asrama</h2>
        <div class="card-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="<?php echo $row['foto_profil'] ?: 'default.png'; ?>" alt="Foto Karyawan">
                        <h2><?php echo htmlspecialchars($row['nama_lengkap']); ?></h2>
                        <p><strong>Telepon:</strong> <?php echo htmlspecialchars($row['nomor_telepon']); ?></p>
                        <p><strong>Jabatan:</strong> <?php echo htmlspecialchars($row['jabatan']); ?></p>
                        <p class="status <?php echo ($row['status'] == 'Aktif') ? 'aktif' : 'tidak-aktif'; ?>">
                            Status: <?php echo htmlspecialchars($row['status']); ?>
                        </p>
                        <form action="" method="GET" style="display:inline;">
                            <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form action="" method="GET" style="display:inline;">
                            <input type="hidden" name="hapus_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada data</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
