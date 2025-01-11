<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">

</head>

<body>
<nav>
    <div class="logo">
        <i class="bi bi-people-fill"> Kita Dorm </i>
    </div>
    <div class="right-icons dropdown">
        <i class="bi bi-bell-fill"></i>
        <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
            <span>Admin</span>
        </div>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item text-danger" href="loginadmin.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>
</nav>

    <div class="sidebar">
        <a href="#"><i class="fas fa-desktop"></i><span>Dashboard</span></a>
        <a href="pembayaranadmin.php"><i class="fas fa-list-ul"></i><span>Pembayaran Mahasiswa</span></a>
        <a href="keluhanadmin.php"><i class="fas fa-pencil-alt"></i><span>Keluhan Mahasiswa</span></a>
        <a href="pengelola.php"><i class="fas fa-address-card"></i><span>Halaman Pengelola</span></a>
    </div>

    <div class="content">
        <div class="vision">
            <img src="depan.jpg" alt="images">
            <img src="image/images (1).jpeg.jpg" alt="images">
            <img src="image/images (2).jpeg.jpg" alt="Bunk Bed">
        </div>
        <table class="visi-misi">
            <tr>
                <td class="visi">Visi</td>
                <td class="misi">Misi</td>
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

    <div class="footer">
        <h4>HUBUNGI KITA</h4>
        <div>
            <h5 style="display: inline;">0851-5679-8811</h5>
            <h5 style="display: inline; margin-left: 10px;">@asramakitaofficial</h5>
            <h5 style="display: inline; margin-left: 10px;">asramakita@gmail.com</h5>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>