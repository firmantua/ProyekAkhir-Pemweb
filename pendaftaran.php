<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database menggunakan mysqli dengan OOP
$host = "localhost";
$user = "root";
$password = "";
$dbname = "asramakita";

$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Mendapatkan email dari sesi
$email = $_SESSION['user_email'];

// Mengambil full_name dan nim dari tabel mahasiswa berdasarkan email
$stmt_mahasiswa = $conn->prepare("SELECT full_name, nim FROM mahasiswa WHERE email = ?");
if (!$stmt_mahasiswa) {
    die("Prepare failed: " . $conn->error);
}
$stmt_mahasiswa->bind_param("s", $email);
$stmt_mahasiswa->execute();
$result_mahasiswa = $stmt_mahasiswa->get_result();

if ($result_mahasiswa->num_rows > 0) {
    $data_mahasiswa = $result_mahasiswa->fetch_assoc();
    $full_name = htmlspecialchars($data_mahasiswa['full_name'], ENT_QUOTES, 'UTF-8');
    $nim = htmlspecialchars($data_mahasiswa['nim'], ENT_QUOTES, 'UTF-8');
} else {
    echo "<script>alert('Data mahasiswa tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt_mahasiswa->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Kita Dorm</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* NAVBAR STYLING */
        nav {
            background-color: #34495e;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
        }

        nav .logo i {
            margin-right: 10px;
        }

        nav .right-icons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        nav .right-icons i {
            font-size: 1.2rem;
            color: #fff;
        }

        nav .right-icons span {
            font-size: 1rem;
        }

        /* CONTENT STYLING */
        .content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #004a99;
            margin-bottom: 20px;
        }

        h2 {
            color: #0066cc;
            margin-bottom: 10px;
        }

        ol {
            padding-left: 20px;
            list-style: decimal;
        }

        ol li {
            margin: 10px 0;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* BUTTON STYLING */
        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .actions button,
        .actions a {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .actions .back {
            background-color: #ccc;
            color: #333;
        }

        .actions .back:hover {
            background-color: #bbb;
        }

        .actions .submit {
            background-color: #0066cc;
            color: #fff;
            text-decoration: none;
        }

        .actions .submit:hover {
            background-color: #004a99;
        }
    </style>
</head>
<body>
    <nav>
        <a href="home.php" class="logo">
            <i class="fa-solid fa-house-user"></i> Asrama Kita
        </a>
        <div class="right-icons">
            <i class="fa-solid fa-user-circle"></i>
            <span><?php echo $full_name; ?></span>
        </div>
    </nav>
    <div class="content">
        <h1>Pendaftaran</h1>
        <h2>Alur Pendaftaran Penghuni Asrama Mahasiswa Kita</h2>
        <ol>
            <li>Pendaftaran dibuka setiap tahun ajaran baru (Semester Gasal & Semester Genap) untuk mahasiswa baru (Program Vokasi, Sarjana, Magister, dan Doktor) serta mahasiswa lama (semester 3) yang belum pernah menempati Asrama Mahasiswa Kita.</li>
            <li>Informasi mengenai jadwal pendaftaran akan diumumkan di website dan Instagram kami @asramamahasiswakita.</li>
            <li>Mahasiswa melakukan pendaftaran secara online melalui tautan pendaftaran <a href="https://asrama.um.ac.id/wp-content/uploads/2020/07/Formulir-Pendaftaran-PWB-2020-Gelombang-2.pdf" target="_blank">berikut</a>.</li>
            <li>Setelah itu, mahasiswa mengisi biodata dan mengunggah berkas yang tertera di formulir pendaftaran.</li>
            <li>Setelah data lengkap, mahasiswa menunggu konfirmasi dari admin Asrama Mahasiswa Kita.</li>
            <li>Jika mendapatkan konfirmasi melalui email dari admin Asrama Mahasiswa Kita, langkah selanjutnya adalah</li>
            <li>Mahasiswa melakukan pembayaran ke nomor Bank yang akan tertera di halaman Pembayaran calon penghuni Asrama beserta total harganya.</li>
            <li>Setelah pembayaran, mahasiswa akan menerima notifikasi melalui dashboard terkait keberhasilan masuk asrama dan kamar hunian.</li>
        </ol>
    </div>
    <div class="actions">
        <button type="button" class="back" onclick="history.back()">Kembali</button>
        <a href="pendaftaran_lanjut.php" class="submit">Lanjut</a>
    </div>
</body>
</html>
