<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penyewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <!-- <img class="navbar-logo" src="../image/logo-dash.png" alt="logo-rupin"> -->
    <a href="index.php" class="active">Dashboard</a>
    <a href="status_pemesanan.php">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">

    <!-- Navbar Atas di Dalam Konten -->
    <div class="top-nav rounded shadow-sm mb-4">
        <div>
            <a href="../index.php" class="go-home btn btn-outline-secondary btn-sm "><i class="fa-solid fa-chevron-left me-2"></i>Homepage</i></a>
        </div>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small><br>
            <!-- <a href="../logout.php" class="text-danger text-decoration-none btn btn-link btn-sm">Logout</a> -->
        </div>
    </div>

    <!-- Isi Dashboard -->
    <h2>Selamat datang, Penyewa!</h2>
    <p>Ini adalah halaman dashboard khusus untuk penyewa.</p>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>
</html>