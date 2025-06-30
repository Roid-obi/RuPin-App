<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super-admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../../styles/dashboard.css"  rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
        <a href="../index.php">Dashboard</a>
        <a href="../manajemen-ruang-alat/index.php" >Manajemen Ruang & alat</a>
        <a href="./index.php" class="active">Manajemen Pemesanan</a>
        <a href="../manajemen-pembayaran/index.php">Manajemen Pembayaran</a>
        <a href="../manajemen-pengembalian/index.php" >Manajemen Pengembalian</a>
        <a href="../manajemen-laporan-pembayaran/index.php">Manajemen laporan Pembayaran</a>
        <a href="../manajemen-pengguna/index.php" >Manajemen Pengguna</a>
        <a href="../profil.php">Profil Saya</a>
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

        <h2>Halaman Manajemen Pemesanan</h2>
    </div>
</body>
</html>