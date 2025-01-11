<?php
session_start();

// Aktifkan pelaporan kesalahan untuk debugging (nonaktifkan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek apakah user sudah login
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Anda belum login!'); window.location.href='login.php';</script>";
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

// Mengambil full_name, nim, dan kode_kamar dari tabel mahasiswa berdasarkan email
$stmt_mahasiswa = $conn->prepare("SELECT full_name, nim, kode_kamar FROM mahasiswa WHERE email = ?");
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
    $kode_kamar = htmlspecialchars($data_mahasiswa['kode_kamar'], ENT_QUOTES, 'UTF-8');
} else {
    echo "<script>alert('Data mahasiswa tidak ditemukan!'); window.location.href='login.php';</script>";
    exit();
}
$stmt_mahasiswa->close();

// Inisialisasi pesan
$success_message = "";
$error_message = "";

// Proses INSERT saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi dan sanitasi input periode
    $periode = isset($_POST['periode']) ? trim($_POST['periode']) : '';
    $periode = htmlspecialchars($periode, ENT_QUOTES, 'UTF-8');

    // Validasi dan sanitasi input kode_kamar
    $kode_kamar_input = isset($_POST['kode_kamar']) ? trim($_POST['kode_kamar']) : '';
    $kode_kamar_input = htmlspecialchars($kode_kamar_input, ENT_QUOTES, 'UTF-8');

    // Pastikan kode_kamar tidak kosong
    if (empty($kode_kamar_input)) {
        $error_message = "Kode kamar tidak boleh kosong.";
    }

    // Set total pembayaran berdasarkan periode
    $total_pembayaran = 0;
    switch ($periode) {
        case '1 tahun':
            $total_pembayaran = 5000000;
            break;
        case '1 semester':
            $total_pembayaran = 2700000;
            break;
        case '1 bulan':
            $total_pembayaran = 500000;
            break;
        default:
            $error_message = "Periode pembayaran tidak valid.";
            break;
    }

    // Proses upload file jika tidak ada error sebelumnya
    if (empty($error_message)) {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES['file']['name']);
            $file_tmp = $_FILES['file']['tmp_name'];
            $upload_dir = "uploads/";

            // Cek dan buat direktori uploads jika belum ada
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error_message = "Gagal membuat direktori upload.";
                }
            }

            // Buat nama file unik
            $unique_file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file_name);
            $file_path = $upload_dir . $unique_file_name;

            // Validasi jenis file
            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $file_tmp);
            finfo_close($file_info);

            // Validasi ukuran file (maksimal 2MB)
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($file_type, $allowed_types)) {
                $error_message = "Jenis file tidak diizinkan. Hanya PDF, JPG, dan PNG yang diperbolehkan.";
            } elseif ($_FILES['file']['size'] > $max_size) {
                $error_message = "Ukuran file terlalu besar. Maksimal 2MB.";
            } else {
                // Pindahkan file ke direktori uploads
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Menggunakan prepared statements untuk INSERT data
                    $insert_query = "INSERT INTO pembayaran (full_name, nim, kode_kamar, periode, total, file) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($insert_query);
                    if ($stmt_insert) {
                        // bind_param types: s (string), s, s, s, i (integer), s
                        $stmt_insert->bind_param("sssiss", $full_name, $nim, $kode_kamar_input, $periode, $total_pembayaran, $file_path);
                        if ($stmt_insert->execute()) {
                            echo "<script>
                                    alert('Pembayaran berhasil!');
                                    window.location.href = 'home.php';
                                  </script>";
                            exit();
                        } else {
                            $error_message = "Terjadi kesalahan saat memasukkan data: " . $stmt_insert->error;
                        }
                        $stmt_insert->close();
                    } else {
                        $error_message = "Prepare failed untuk INSERT: " . $conn->error;
                    }
                } else {
                    $error_message = "Gagal mengunggah file. Pastikan direktori tersedia dan file valid.";
                }
            }
        } else {
            $error_message = "File tidak boleh kosong atau terjadi kesalahan saat upload.";
        }
    }
}

// Tutup koneksi
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Kita Dorm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling tambahan untuk estetika */
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .alert {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    
    <div class="container mt-5">
        <div class="header">
            <h2>Form Pembayaran Kita Dorm</h2>
        </div>
        
        <!-- Menampilkan pesan sukses atau error -->
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

        <form method="POST" enctype="multipart/form-data">
            <!-- Input Nama dan NIM diambil dari database -->
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo isset($full_name) ? $full_name : ''; ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" name="nim" id="nim" class="form-control" value="<?php echo isset($nim) ? $nim : ''; ?>" readonly>
            </div>

            <!-- Input Kode Kamar (Dropdown) -->
            <div class="mb-3">
                <label for="kode_kamar" class="form-label">Kode Kamar</label>
                <select name="kode_kamar" id="kode_kamar" class="form-select" required>
                    <option value="" disabled>Pilih Kode Kamar</option>
                    <?php
                        // Mengambil kode_kamar dari database
                        $conn = new mysqli($host, $user, $password, $dbname);
                        if ($conn->connect_error) {
                            die("Koneksi database gagal: " . $conn->connect_error);
                        }
                        $result = $conn->query("SELECT kode_kamar FROM kamar");
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                // Pre-select the kode_kamar jika sudah dipilih
                                $selected = ($row['kode_kamar'] == $kode_kamar) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['kode_kamar'], ENT_QUOTES, 'UTF-8') . '" ' . $selected . '>' . htmlspecialchars($row['kode_kamar'], ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                        }
                        $conn->close();
                    ?>
                </select>
            </div>

            <!-- Periode Pembayaran -->
            <div class="mb-3">
                <label for="periode" class="form-label">Periode Pembayaran</label>
                <select name="periode" id="periode" class="form-select" required>
                    <option value="" selected disabled>Pilih Periode Pembayaran</option>
                    <option value="1 tahun" <?php echo (isset($_POST['periode']) && $_POST['periode'] == '1 tahun') ? 'selected' : ''; ?>>1 Tahun - Rp. 5.000.000</option>
                    <option value="1 semester" <?php echo (isset($_POST['periode']) && $_POST['periode'] == '1 semester') ? 'selected' : ''; ?>>1 Semester - Rp. 2.700.000</option>
                    <option value="1 bulan" <?php echo (isset($_POST['periode']) && $_POST['periode'] == '1 bulan') ? 'selected' : ''; ?>>1 Bulan - Rp. 500.000</option>
                </select>
            </div>

            <!-- Informasi Rekening -->
            <div class="mb-3">
                <p><strong>Nomor Rekening:</strong></p>
                <p>525105869374 - <strong>Bank BRI</strong> - Asrama Kita</p>
                <p>082167389046 - <strong>e-Wallet</strong> - Asrama Kita</p>
            </div>

            <!-- Upload Bukti Pembayaran -->
            <div class="mb-3">
                <label for="file" class="form-label">Unggah Bukti Pembayaran</label>
                <input type="file" name="file" class="form-control" id="file" required>
                <div class="form-text">Jenis file yang diizinkan: PDF, JPG, PNG. Maksimal ukuran: 2MB.</div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary w-100">Bayar</button>
        </form>
    </div>

    <!-- Bootstrap JS (Opsional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
