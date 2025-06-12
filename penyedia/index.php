<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyedia') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penyedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #675DFE;
            color: white;
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
        <h4 class="text-center py-3">Rupin - Penyedia</h4>
        <a href="index.php" class="active">Dashboard</a>
        <a href="daftar_pemesanan.php">Daftar Pemesanan</a>
        <a href="kelola_item.php">Kelola Item</a>
        <a href="profil.php">Profil Saya</a>
        <a href="../logout.php" class="text-danger">Logout</a>
    </div>
    <div class="content">
        <h2>Selamat datang, Penyedia!</h2>
        <p>Ini adalah halaman dashboard khusus untuk penyedia.</p>
    </div>
</body>
</html>