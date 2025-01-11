<?php
// Koneksi ke database
include('koneksi.php');  // Pastikan file database.php sudah benar

// Proses menyimpan data ketika tombol 'Simpan' ditekan
if (isset($_POST['simpan'])) {
    // Ambil data dari form
    $id_edit = $_POST['id_edit'];  // ID edit jika ada
    $nama_lengkap = $_POST['nama_lengkap'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $jabatan = $_POST['jabatan'];
    $status = $_POST['status'];

    // Menangani foto profil
    $foto_path = "";
    if ($_FILES['foto_profil']['error'] == 0) {
        $foto_name = $_FILES['foto_profil']['name'];
        $foto_tmp = $_FILES['foto_profil']['tmp_name'];
        $foto_path = 'upload/' . time() . '_' . $foto_name;

        // Pindahkan file foto ke folder upload
        if (!move_uploaded_file($foto_tmp, $foto_path)) {
            echo "Error: Foto tidak bisa di-upload.";
        }
    } else {
        // Jika tidak ada foto, biarkan path foto kosong atau ambil foto lama jika editing
        $foto_path = $_POST['foto_profil_lama'];
    }

    // Jika id_edit ada, berarti kita mengedit data, kalau tidak, berarti menambah data baru
    if ($id_edit) {
        // Query untuk update data
        $query = "UPDATE pengelola_asrama SET 
                  nama_lengkap='$nama_lengkap', 
                  nomor_telepon='$nomor_telepon', 
                  jabatan='$jabatan', 
                  status='$status', 
                  foto_profil='$foto_path' 
                  WHERE id=$id_edit";
    } else {
        // Query untuk insert data baru
        $query = "INSERT INTO pengelola_asrama (nama_lengkap, nomor_telepon, jabatan, status, foto_profil) 
                  VALUES ('$nama_lengkap', '$nomor_telepon', '$jabatan', '$status', '$foto_path')";
    }

    // Eksekusi query dan beri feedback
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil disimpan!');</script>";
        header("Location: admin.php");  // Refresh halaman setelah simpan
        exit(); // Pastikan tidak ada kode yang dieksekusi setelah redirect
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Jika ada id_edit di URL, maka tampilkan data untuk diedit
$id_edit = isset($_GET['edit_id']) ? $_GET['edit_id'] : null;
if ($id_edit) {
    $result = mysqli_query($conn, "SELECT * FROM pengelola_asrama WHERE id=$id_edit");
    $row = mysqli_fetch_assoc($result);
} else {
    $row = null;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pengelola Asrama</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   
    <h2>Daftar Pengelola</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Nomor Telepon</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Menampilkan daftar pengelola
            $result = mysqli_query($conn, "SELECT * FROM pengelola_asrama");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['nama_lengkap']}</td>";
                echo "<td>{$row['nomor_telepon']}</td>";
                echo "<td>{$row['jabatan']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td><img src='{$row['foto_profil']}' width='50'></td>";
                echo "<td><a href='admin.php?edit_id={$row['id']}'>Edit</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
