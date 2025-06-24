<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyedia') {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
    <style>
        .profile-card {
            max-width: 300px;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="daftar_pemesanan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'daftar_pemesanan.php' ? 'active' : '' ?>">Daftar Pemesanan</a>
    <a href="kelola_item.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tambah_item.php' ? 'active' : '' ?>">Kelola Item</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
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
                <a href="../logout.php" class="btn btn-danger w-100 py-2 mb-2">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>