<?php
session_start();
include '../config.php';

// Autentikasi penyedia
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyedia') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $con->prepare("SELECT nama FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Statistik sederhana
// Jumlah item
$item_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM items WHERE user_id = $user_id");
$item_total = mysqli_fetch_assoc($item_result)['total'] ?? 0;

// Total pemesanan
$booking_result = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM booking 
    JOIN items ON booking.item_id = items.item_id 
    WHERE items.user_id = $user_id
");
$booking_total = mysqli_fetch_assoc($booking_result)['total'] ?? 0;

// Pemesanan selesai
$selesai_result = mysqli_query($con, "
    SELECT COUNT(*) AS total FROM booking 
    JOIN items ON booking.item_id = items.item_id 
    WHERE items.user_id = $user_id AND booking.status = 'selesai'
");
$selesai_total = mysqli_fetch_assoc($selesai_result)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penyedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css" rel="stylesheet">
    <style>
        .card-stat {
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.04);
            padding: 20px;
            text-align: center;
        }
        .card-stat h3 {
            font-size: 2rem;
            margin-bottom: 0;
        }
        .card-stat small {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
        <a href="index.php" class="active">Dashboard</a>
        <a href="daftar_pemesanan.php">Daftar Pemesanan</a>
        <a href="kelola_item.php">Kelola Item</a>
        <a href="laporan_keterlambatan.php">Lapor Keterlambatan</a>
        <a href="profil.php">Profil Saya</a>
    </div>

    <!-- Konten -->
    <div class="content">
        <!-- Navbar Top -->
        <div class="top-nav rounded shadow-sm mb-4 d-flex justify-content-between align-items-center">
            <a href="../index.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Homepage</a>
            <div class="text-end">
                <small>Halo, <?= htmlspecialchars($user['nama']) ?> (<?= ucfirst($_SESSION['role']) ?>)</small>
            </div>
        </div>

        <h2 class="mb-4">Selamat datang, <?= htmlspecialchars($user['nama']) ?>!</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-stat bg-light">
                    <h3><?= $item_total ?></h3>
                    <small>Item yang Disewakan</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-stat bg-light">
                    <h3><?= $booking_total ?></h3>
                    <small>Total Pemesanan</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-stat bg-light">
                    <h3><?= $selesai_total ?></h3>
                    <small>Pemesanan Selesai</small>
                </div>
            </div>
        </div>

        <p class="mt-4">Gunakan menu di sebelah kiri untuk mengelola pemesanan dan item yang kamu sediakan.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
