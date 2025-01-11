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

// Mendapatkan nama, NIM, dan nomor kamar dari tabel mahasiswa berdasarkan email sesi
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT full_name, nim, kode_kamar FROM mahasiswa WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_lengkap = $data['full_name'];
        $nim = $data['nim'];
        $kode_kamar = $data['kode_kamar'];
    } else {
        echo "<script>alert('Data mahasiswa tidak ditemukan!'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
    exit();
}

// Proses INSERT saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $kode_kamar = mysqli_real_escape_string($conn, $_POST['nokamar']);
    $tanggal_keluhan = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keluhan = mysqli_real_escape_string($conn, $_POST['keluhan']);

    // Query INSERT data ke database
    $query = "INSERT INTO keluhan (nama_lengkap, nim, kode_kamar, tanggal_keluhan, keluhan) 
              VALUES ('$nama_lengkap','$nim', '$kode_kamar', '$tanggal_keluhan', '$keluhan')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Keluhan berhasil diajukan!');
                window.location.href = 'keluhanlanjut.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Keluhan Kita Dorm</title>
    <style>
        /* Umum */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1, h2 {
            color: #333;
            text-align: center;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: bold;
            font-size: 16px;
        }

        input, select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="date"] {
            cursor: pointer;
        }

        button {
            background-color: #333;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #555;
        }

        /* Link styling */
        .button-link {
            display: inline-block;
            margin: 10px auto;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
        }

        .button-link:hover {
            background-color: #0056b3;
        }

        /* Responsiveness */
        @media screen and (max-width: 768px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajukan Keluhan</h1>
        <div class="submit-container">
            <a href="keluhanlanjut.php" class="button-link">Cek Keluhan</a>
        </div>

        <h2>Form Pengajuan Keluhan Kita Dorm</h2>
        <form method="POST" action="">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap); ?>" required>

            <label for="nim">NIM</label>
            <input type="text" name="nim" id="nim" value="<?php echo htmlspecialchars($nim); ?>" required>

            <label for="nokamar">Nomor Kamar</label>
            <input type="text" name="nokamar" id="nokamar" value="<?php echo htmlspecialchars($kode_kamar); ?>" required>

            <label for="tanggal">Tanggal Keluhan</label>
            <input type="date" name="tanggal" id="tanggal" required>

            <label for="keluhan">Isi Keluhan</label>
            <input type="text" name="keluhan" id="keluhan" placeholder="Masukkan Keluhan" required>

            <button type="submit">Ajukan Keluhan</button>
        </form>
    </div>
</body>
</html>
