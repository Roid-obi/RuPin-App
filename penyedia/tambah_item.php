<?php
include '../session.php';
include '../config.php';

// Pastikan user adalah penyedia
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyedia') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $tipe = mysqli_real_escape_string($con, $_POST['tipe']);
    $harga_sewa = floatval($_POST['harga_sewa']);
    $lokasi = mysqli_real_escape_string($con, $_POST['lokasi']);
    $deskripsi = mysqli_real_escape_string($con, $_POST['deskripsi']);
    $user_id = $_SESSION['user_id'];

    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $filename = basename($_FILES['gambar']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $ext;
        $target_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
            $gambar = $new_filename;
        }
    }

    $query = "INSERT INTO items (user_id, nama, tipe, harga_sewa, lokasi, status, gambar, deskripsi)
              VALUES (?, ?, ?, ?, ?, 'tersedia', ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("issdsss", $user_id, $nama, $tipe, $harga_sewa, $lokasi, $gambar, $deskripsi);
    $stmt->execute();

    header("Location: kelola_item.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
    <style>
        #preview {
            max-width: 200px;
            margin-top: 10px;
            display: none;
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
    <a href="laporan_keterlambatan.php">Lapor Keterlambatan</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <!-- Navbar Atas -->
    <div class="top-nav rounded shadow-sm mb-4">
        <div>
            <a href="../index.php" class="go-home btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-chevron-left me-2"></i>Homepage
            </a>
        </div>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>
    
    <h2>Tambah Item</h2>

    <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Item:</label>
            <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Item" required>
            <div class="invalid-feedback">Nama item harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="tipe" class="form-label">Tipe:</label>
            <select name="tipe" id="tipe" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="ruang">Ruang</option>
                <option value="alat">Alat</option>
            </select>
            <div class="invalid-feedback">Silakan pilih tipe item.</div>
        </div>

        <div class="mb-3">
            <label for="harga_sewa" class="form-label">Harga Sewa/Hari:</label>
            <input type="number" name="harga_sewa" id="harga_sewa" class="form-control" placeholder="Harga Sewa" required>
            <div class="invalid-feedback">Harga sewa harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi:</label>
            <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Lokasi" required>
            <div class="invalid-feedback">Lokasi harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi:</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Deskripsi item..." required></textarea>
            <div class="invalid-feedback">Deskripsi harus diisi.</div>
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar:</label>
            <input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewImage(event)" class="form-control" required>
            <div class="invalid-feedback">Silakan pilih gambar.</div>
        </div>

        <div class="mb-3">
            <img id="preview" src="#" alt="Preview Gambar">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="kelola_item.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const img = document.getElementById('preview');
        img.src = reader.result;
        img.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}

// Validasi Form Bootstrap
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>
