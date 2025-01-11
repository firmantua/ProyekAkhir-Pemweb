<?php
include 'db.php';

// Ambil data kamar dari database
$query = "SELECT * FROM kamar";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan Kamar</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
    <a href="home.php" class="logo">
            <i class="fa-solid fa-house-user"></i> Asrama Kita
        </a>
    </div>

    <div class="content">
        <h2>Pemilihan Kamar</h2>
        <div class="room-container">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <?php
                $status = ($row['terisi'] >= $row['kapasitas']) ? 'penuh' : 'tersedia';
                ?>
                <div class="room <?= $status; ?>">
                    <a href="detail_kamar.php?kode=<?= $row['kode_kamar']; ?>">
                        <?= $row['kode_kamar']; ?>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="status">
            <h3>Status Kamar</h3>
            <p><span class="red-dot"></span> Penuh</p>
            <p><span class="gray-dot"></span> Tersedia</p>
        </div>
        <button class="btn">Kembali</button>
    </div>
</body>
</html>
