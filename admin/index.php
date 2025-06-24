<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
        <a href="index.php" class="active">Dashboard</a>
        <a href="konfirmasi_pembayaran.php">Konfirmasi Pembayaran</a>
        <a href="kelola_user.php">Kelola User</a>
        <a href="laporan_transaksi.php">Laporan Transaksi</a>
        <a href="profil.php">Profil Saya</a>
    </div>
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

        <h2>Selamat datang, Admin!</h2>
        <p>Ini adalah halaman dashboard khusus untuk admin.</p>
    </div>
</body>
</html>