<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    die("Booking ID tidak ditemukan.");
}

$booking_id = intval($_GET['booking_id']);

$sql = "SELECT p.jumlah, p.metode, i.nama AS nama_item, i.harga_sewa
        FROM pembayaran p
        JOIN pemesanan pm ON p.booking_id = pm.booking_id
        JOIN items i ON pm.item_id = i.item_id
        WHERE p.booking_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data pembayaran tidak ditemukan.");
}

$data = $result->fetch_assoc();
$biaya_admin = $data['harga_sewa'] * 0.05;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
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

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center py-3">Rupin - Penyewa</h4>
    <a href="index.php">Dashboard</a>
    <a href="cari_item.php" class="active">Cari Item</a>
    <a href="status_pemesanan.php">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h2>Konfirmasi Pembayaran</h2>
    
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Item:</strong> <?= htmlspecialchars($data['nama_item']) ?></p>
            <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($data['metode']) ?></p>
            <p><strong>Harga Sewa:</strong> Rp <?= number_format($data['harga_sewa'], 0, ',', '.') ?></p>
            <p><strong>Biaya Admin (5%):</strong> Rp <?= number_format($biaya_admin, 0, ',', '.') ?></p>
            <h5><strong>Total yang Harus Dibayar:</strong> Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></h5>
        </div>
    </div>

    <p>Silakan lakukan pembayaran dan tunggu konfirmasi dari admin.</p>
    <a href="status_pemesanan.php" class="btn btn-primary">Lihat Status Pemesanan</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>