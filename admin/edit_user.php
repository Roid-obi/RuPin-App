<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $role = mysqli_real_escape_string($con, $_POST['role']);

    $stmt = $con->prepare("UPDATE users SET nama=?, email=?, role=? WHERE user_id=?");
    $stmt->bind_param("sssi", $nama, $email, $role, $id);
    $stmt->execute();

    header("Location: kelola_user.php");
    exit;
} else {
    $stmt = $con->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        die("Pengguna tidak ditemukan.");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="konfirmasi_pembayaran.php" class="<?= basename($_SERVER['PHP_SELF']) == 'konfirmasi_pembayaran.php' ? 'active' : '' ?>">Konfirmasi Pembayaran</a>
    <a href="kelola_user.php" class="<?= basename($_SERVER['PHP_SELF']) == 'edit_user.php' ? 'active' : '' ?>">Kelola User</a>
    <a href="laporan_transaksi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan_transaksi.php' ? 'active' : '' ?>">Laporan Transaksi</a>
    <a href="hitung_denda.php">Hitung Denda</a>
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

    <h2>Edit Pengguna</h2>

    <form method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama:</label>
            <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($user['nama']) ?>" class="form-control" required>
            <div class="invalid-feedback">Nama harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
            <div class="invalid-feedback">Email harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select name="role" id="role" class="form-select" required>
                <option value="">-- Pilih Role --</option>
                <option value="penyewa" <?= $user['role'] === 'penyewa' ? 'selected' : '' ?>>Penyewa</option>
                <option value="penyedia" <?= $user['role'] === 'penyedia' ? 'selected' : '' ?>>Penyedia</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <div class="invalid-feedback">Silakan pilih role pengguna.</div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="kelola_user.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
// Validasi Form Bootstrap
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>