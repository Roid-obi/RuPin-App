<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Pengguna tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            background-color: #f8f9fa;
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
            display: flex;
            align-items: start;
        }
        .profile-card {
            max-width: 300px;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center py-3">Rupin - Admin</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="konfirmasi_pembayaran.php" class="<?= basename($_SERVER['PHP_SELF']) == 'konfirmasi_pembayaran.php' ? 'active' : '' ?>">Konfirmasi Pembayaran</a>
    <a href="kelola_user.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kelola_user.php' ? 'active' : '' ?>">Kelola User</a>
    <a href="laporan_transaksi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan_transaksi.php' ? 'active' : '' ?>">Laporan Transaksi</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <div class="profile-card">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <!-- Avatar Placeholder -->
                <img src="../image/place-user.jpeg"  alt="Foto Profil" class="rounded-circle mb-3" style="width: 100px; height: 100px;">
                
                <h4><?= htmlspecialchars($user['nama']) ?></h4>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                <hr>
                <p><strong>Role:</strong> <?= ucfirst(htmlspecialchars($user['role'])) ?></p>
                <p><strong>Status Akun:</strong> Aktif</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>