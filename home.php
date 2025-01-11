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

// Mengambil kode kamar dari tabel mahasiswa berdasarkan nim
$stmt_kamar = $conn->prepare("SELECT kode_kamar FROM mahasiswa WHERE nim = ?");
if (!$stmt_kamar) {
    die("Prepare failed: " . $conn->error);
}
$stmt_kamar->bind_param("s", $nim);
$stmt_kamar->execute();
$result_kamar = $stmt_kamar->get_result();

$kode_kamar = null;
if ($result_kamar->num_rows > 0) {
    $data_kamar = $result_kamar->fetch_assoc();
    $kode_kamar = htmlspecialchars($data_kamar['kode_kamar'], ENT_QUOTES, 'UTF-8');
}
$stmt_kamar->close();

// Mengambil status pembayaran terbaru dari tabel pembayaran
$status1 = ""; // Inisialisasi variabel status1

if ($kode_kamar) {
    $stmt_status = $conn->prepare("SELECT status1 FROM pembayaran WHERE nim = ? AND kode_kamar = ? ORDER BY id_pem DESC LIMIT 1");
    if ($stmt_status) {
        $stmt_status->bind_param("ss", $nim, $kode_kamar);
        $stmt_status->execute();
        $result_status = $stmt_status->get_result();
        if ($result_status->num_rows > 0) {
            $data_status = $result_status->fetch_assoc();
            $status_raw = strtolower($data_status['status1']); // Pastikan status dalam huruf kecil
            if ($status_raw === 'selesai') {
                $status1 = '<span class="badge bg-success">Selesai</span>';
            } elseif ($status_raw === 'belum') {
                $status1 = '<span class="badge bg-danger">Belum</span>';
            } elseif ($status_raw === 'proses') {
                $status1 = '<span class="badge bg-warning">Proses</span>';
            } else {
                // Jika status tidak sesuai, tampilkan sebagai teks biasa
                $status1 = '<span class="badge bg-secondary">' . htmlspecialchars($data_status['status1'], ENT_QUOTES, 'UTF-8') . '</span>';
            }
        } else {
            // Jika tidak ada pembayaran, tampilkan status "Belum"
            $status1 = '<span class="badge bg-danger">Belum</span>';
        }
        $stmt_status->close();
    } else {
        // Jika prepare gagal, tampilkan pesan kesalahan atau handle sesuai kebutuhan
        $status1 = '<span class="badge bg-warning">Status Tidak Tersedia</span>';
    }
} else {
    // Jika tidak ada kode_kamar, tidak perlu menampilkan status
    $status1 = '';
}

// Tutup koneksi setelah semua operasi pengambilan data selesai
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamar Pilihan Anda Sudah</title>
    <!-- Menggunakan Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Menggunakan Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
/* Styling untuk body */
body {
            font-family: 'Arial', sans-serif;
            background-color: #2c3e50;
            color: #ecf0f1;
            margin: 0;
            padding: 0;
        }

        /* Navigation */
        nav {
            background-color: #34495e;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        nav .logo i {
            font-size: 1.8rem;
            transition: transform 0.3s ease;
        }

        nav:hover .logo i {
            transform: rotate(360deg);
        }

        nav .right-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        nav .right-icons i {
            font-size: 1.3rem;
            margin-right: 10px;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        nav .right-icons i:hover {
            transform: translateX(10px);
            color: #3498db;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 200px;
            height: 100%;
            background-color: #34495e;
            padding-top: 20px;
            box-shadow: 4px 0 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 22px;
            color: #ecf0f1;
            text-decoration: none;
            transition: background 0.3s ease, transform 0.3s ease;
            font-size: 1rem;
        }

        .sidebar a:hover {
            background-color: #2980b9;
            transform: translateX(10px);
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.3rem;
        }

        /* Content */
        .content {
            margin-left: 220px;
            padding: 20px;
            background-color: #2c3e50;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .content:hover {
            background-color: #34495e;
        }

        /* Kamar Box */
        .kamar-box {
            background: #34495e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .kamar-box:hover {
            transform: scale(1.05);
        }

        .kamar-box h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #ecf0f1;
        }

        .kamar-box p {
            font-size: 1.2rem;
            color: #ecf0f1;
        }

        .kamar-box strong {
            color: #3498db;
        }

        /* Vision & Mission Section */
        .vision img {
            width: 100%;
            max-width: 320px;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .vision img:hover {
            transform: scale(1.1);
        }

        .visi-misi {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .visi-misi td {
            padding: 15px;
            border: 1px solid #bdc3c7;
            text-align: center;
            background-color: #34495e;
            color: #ecf0f1;
            transition: background-color 0.3s ease;
        }

        .visi-misi td:hover {
            background-color: #2980b9;
        }
        a {
            text-decoration: none;
        }
        .no-underline {
        text-decoration: none;
        }
        .visi-misi td.visi {
            background-color: #2980b9;
        }

        .visi-misi td.misi {
            background-color: #16a085;
        }

        /* Modal Styling */
        .modal-body {
            color: rgb(0, 0, 0);
        }

        .modal-content {
            border-radius: 8px;
        }

        .modal-header {
            background-color: rgb(214, 202, 42);
            color: #fff;
        }

        .modal-footer {
            border-top: 1px solid rgb(167, 103, 31);
        }

        .alert-success,
        .alert-danger {
            margin-top: 20px;
        }

        /* Badge Status */
        .badge {
            font-size: 1rem;
            padding: 5px 10px;
        }

        /* Footer */
        footer {
            background-color: #34495e;
            color: #ecf0f1;
            text-align: center;
            padding: 15px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 30px;
        }

        footer:hover {
            background-color: #2980b9;
        }

        /* Animation for Bell Icon */
        #notif-icon {
            font-size: 1.5rem;
            position: relative;
            cursor: pointer;
            animation: bell-shake 0.5s ease-in-out infinite;
        }

        @keyframes bell-shake {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            50% { transform: rotate(10deg); }
            75% { transform: rotate(-10deg); }
            100% { transform: rotate(0deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }

            nav {
                flex-direction: column;
            }

            .logo {
                margin-bottom: 10px;
            }

            .right-icons {
                flex-direction: column;
                align-items: flex-start;
            }
            nav .logo {
                display: flex;
                align-items: center;
                margin-bottom: 20px; /* Tambahkan jarak di bawah logo */
            }
            .right-icons i {
                margin: 5px 0;
            }
        }
    </style>
</head>

<body>
      <!-- Navigation -->
      <nav>
      <a href="home.php" class="logo">
            <i class="fa-solid fa-house-user"></i> Asrama Kita
        </a>
        <div class="right-icons">
            <i class="fa-solid fa-bell" id="notif-icon" data-bs-toggle="modal" data-bs-target="#notifModal"></i>
            <i class="fa-solid fa-user-circle"></i>
            <span><?php echo htmlspecialchars($full_name); ?></span>
        </div>
        </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="home.php"><i class="fa-solid fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="profile.php"><i class="fa-solid fa-user"></i><span>Profil Mahasiswa</span></a>
        <a href="pendaftaran.php"><i class="fa-solid fa-table"></i><span>Pendaftaran</span></a>
        <a href="keluhan.php"><i class="fa-solid fa-list-ul"></i><span>Ajukan Keluhan</span></a>
        <a href="pengelola_asrama.php"><i class="fa-solid fa-pencil-alt"></i><span>Pengelola</span></a>
        <a href="tampilan.php"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span></a>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Box Kamar -->
        <div class="kamar-box">
            <h3>Kamar Yang Anda Pilih</h3>
            <?php if ($kode_kamar): ?>
                <p>Kode Kamar: <strong><?php echo $kode_kamar; ?></strong></p>
                <p>Status : <strong><?php echo $status1; ?></strong></p>
            <?php else: ?>
                <p>Anda belum mendapatkan kamar.</p>
            <?php endif; ?>
        </div>

        <!-- Menampilkan Pesan Sukses atau Error (jika ada) -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <h2>Selamat Datang, <?php echo $full_name; ?>!</h2>
        </div>

        <!-- Konten Tambahan -->
        <div class="vision d-flex flex-wrap justify-content-center">
            <img src="image/images.jpeg.jpg" alt="images">
            <img src="image/images (1).jpeg.jpg" alt="images">
            <img src="image/images (2).jpeg.jpg" alt="Bunk Bed">
        </div>
        <table class="visi-misi">
            <tr>
                <td class="visi"><strong>Visi</strong></td>
                <td class="misi"><strong>Misi</strong></td>
            </tr>
            <tr>
                <td class="visi">
                    Menjadi asrama mahasiswa yang membentuk generasi unggul, berkarakter, dan berintegritas
                </td>
                <td class="misi">
                    Mendukung pengembangan mahasiswa dan menciptakan lingkungan asrama yang nyaman dan disiplin
                </td>
            </tr>
        </table>
    </div>

<!-- Modal Notifikasi -->
<div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notifModalLabel">Notifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Status Pembayaran</h6>
                    <p>Kode Kamar: <strong><?php echo $kode_kamar; ?></strong></p>
                    <p>Status: <?php echo $status1; ?></p>
                    <hr>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS (Opsional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS (Opsional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>

</html>
