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

// Mendapatkan nama dan NIM dari tabel mahasiswa berdasarkan email sesi
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

if ($email) {
    $query = "SELECT full_name, nim FROM mahasiswa WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $full_name = $data['full_name'];
        $nim = $data['nim'];
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
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);

    // Proses upload file
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $upload_dir = "uploads/";

    // Cek direktori uploads
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $unique_file_name = time() . '_' . basename($file_name);
    $file_path = $upload_dir . $unique_file_name;

    // Validasi file dan upload
    if (!empty($file_name) && move_uploaded_file($file_tmp, $file_path)) {
        // Query INSERT data ke database
        $query = "INSERT INTO pendaftaran (nim, file) 
                  VALUES ('$nim', '$file_path')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Pendaftaran berhasil!');
                    window.location.href = 'kamar.php';
                  </script>";
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Gagal mengunggah file. Pastikan file valid dan direktori tersedia.";
    }
}
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
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
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
    <div class="container">
        <h2>Form Pendaftaran Kita Dorm</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <label for="full_name">Nama Lengkap</label>
            <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($full_name); ?>" readonly>

            <label for="nim">NIM</label>
            <input type="text" name="nim" id="nim" value="<?php echo htmlspecialchars($nim); ?>" required>

            <label for="file">Unggah File</label>
            <input type="file" name="file" id="file" required>

            <button type="submit">Daftar</button>
        </form>
    </div>
</body>
</html>

  