<?php
session_start();
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data jumlah booking
$sql_booking = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) AS menunggu,
    SUM(CASE WHEN status = 'dibatalkan' THEN 1 ELSE 0 END) AS dibatalkan,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) AS selesai
    FROM booking WHERE user_id = ?";
$stmt = $con->prepare($sql_booking);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penyewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css" rel="stylesheet">
    <style>
        .stat-card {
            border-left: 5px solid var(--bs-primary);
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            padding: 1.2rem;
            border-radius: 0.75rem;
            margin-bottom: 1.2rem;
        }

        .stat-card h6 {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .stat-card h3 {
            font-size: 1.8rem;
            margin-top: 0.3rem;
            color: #343a40;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php" class="active">Dashboard</a>
    <a href="status_pemesanan.php">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <!-- Navbar Atas -->
    <div class="top-nav rounded shadow-sm mb-4 d-flex justify-content-between align-items-center">
        <a href="../index.php" class="go-home btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Homepage</a>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>

    <!-- Judul -->
    <h2 class="mb-4">Selamat datang, Penyewa!</h2>
    <p>Berikut ringkasan aktivitas sewa kamu:</p>

    <!-- Statistik -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card border-primary">
                <h6>Total Pemesanan</h6>
                <h3><?= $data['total'] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-warning">
                <h6>Status Menunggu</h6>
                <h3><?= $data['menunggu'] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-success">
                <h6>Status Selesai</h6>
                <h3><?= $data['selesai'] ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-danger">
                <h6>Status Dibatalkan</h6>
                <h3><?= $data['dibatalkan'] ?></h3>
            </div>
        </div>
    </div>

    <!-- Aksi -->
    <div class="mt-4">
        <a href="cari_item.php" class="btn btn-primary"><i class="fas fa-search me-2"></i>Cari Ruang / Alat</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>
</html>
