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
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #675DFE;
            color: white;
            position: fixed; /* Sidebar tetap di tempat */
            top: 0;
            left: 0;
            height: 100vh; /* Penuh dari atas ke bawah */
            overflow-y: auto; /* Jika isi terlalu panjang */
        }

        .content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px; /* Agar konten tidak tertutup sidebar */
        }
        
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 1rem;
        }
        .sidebar a:hover,
        .sidebar .active {
            background-color: #574ee5;
        }
        .content {
            flex: 1;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center py-3">Rupin - Admin</h4>
        <a href="index.php" class="active">Dashboard</a>
        <a href="konfirmasi_pembayaran.php">Konfirmasi Pembayaran</a>
        <a href="kelola_user.php">Kelola User</a>
        <a href="laporan_transaksi.php">Laporan Transaksi</a>
        <a href="profil.php">Profil Saya</a>
        <a href="../logout.php" class="text-danger">Logout</a>
    </div>
    <div class="content">
        <h2>Selamat datang, Admin!</h2>
        <p>Ini adalah halaman dashboard khusus untuk admin.</p>
    </div>
</body>
</html>