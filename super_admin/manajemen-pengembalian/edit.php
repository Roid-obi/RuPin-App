<?php
session_start();
include('../../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super-admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil ID dari parameter GET
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Ambil data pengembalian
$stmt = $con->prepare("SELECT * FROM pengembalian WHERE pengembalian_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// Ambil daftar booking untuk select
$booking_result = $con->query("SELECT b.booking_id, i.nama AS item_nama FROM booking b JOIN items i ON b.item_id = i.item_id");

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status = $_POST['status'];
    $denda = $_POST['denda'];

    $bukti = $data['bukti'];
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "../../uploads/";
        $bukti = basename($_FILES['bukti']['name']);
        $target_file = $target_dir . $bukti;
        move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file);
    }

    $stmt = $con->prepare("UPDATE pengembalian SET booking_id = ?, tanggal_kembali = ?, status = ?, denda = ?, bukti = ? WHERE pengembalian_id = ?");
    $stmt->bind_param("issdsi", $booking_id, $tanggal_kembali, $status, $denda, $bukti, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengembalian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../../styles/dashboard.css" rel="stylesheet">
    <style>
        .preview-img {
            max-height: 150px;
            margin-top: 10px;
            display: block;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="../index.php">Dashboard</a>
    <a href="../manajemen-ruang-alat/index.php">Manajemen Ruang & alat</a>
    <a href="../manajemen-pemesanan/index.php">Manajemen Pemesanan</a>
    <a href="../manajemen-pembayaran/index.php">Manajemen Pembayaran</a>
    <a href="./index.php" class="active">Manajemen Pengembalian</a>
    <a href="../manajemen-laporan-pembayaran/index.php">Manajemen laporan Pembayaran</a>
    <a href="../manajemen-pengguna/index.php">Manajemen Pengguna</a>
    <a href="../profil.php">Profil Saya</a>
</div>
<div class="content">
    <div class="top-nav rounded shadow-sm mb-4 d-flex justify-content-between align-items-center">
        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i> Kembali</a>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>

    <h2>Edit Data Pengembalian</h2>

    <form method="POST" enctype="multipart/form-data" class="mt-3">
        <div class="mb-3">
            <label for="booking_id" class="form-label">Booking</label>
            <select name="booking_id" id="booking_id" class="form-control" required>
                <option value="">-- Pilih Booking --</option>
                <?php while ($row = $booking_result->fetch_assoc()): ?>
                    <option value="<?= $row['booking_id'] ?>" <?= $data['booking_id'] == $row['booking_id'] ? 'selected' : '' ?>>Booking #<?= $row['booking_id'] ?> - <?= htmlspecialchars($row['item_nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control" value="<?= $data['tanggal_kembali'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="tepat waktu" <?= $data['status'] === 'tepat waktu' ? 'selected' : '' ?>>Tepat Waktu</option>
                <option value="terlambat" <?= $data['status'] === 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                <option value="hilang/rusak" <?= $data['status'] === 'hilang/rusak' ? 'selected' : '' ?>>Hilang/Rusak</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="denda" class="form-label">Denda (Rp)</label>
            <input type="number" step="0.01" name="denda" id="denda" class="form-control" value="<?= $data['denda'] ?>">
        </div>
        <div class="mb-3">
            <label for="bukti" class="form-label">Bukti Pengembalian</label>
            <input type="file" class="form-control" name="bukti" id="bukti" accept="image/*" onchange="previewImage(event)">
            <?php if ($data['bukti']) : ?>
                <img id="preview" class="preview-img" src="../../uploads/<?= $data['bukti'] ?>" alt="Preview Gambar">
            <?php else : ?>
                <img id="preview" class="preview-img" style="display:none" alt="Preview Gambar">
            <?php endif; ?>
        </div>
        <div class="mb-3 d-flex">
            <button type="submit" class="btn btn-primary me-2"><i class="fa fa-save"></i> Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<script>
function previewImage(event) {
    const input = event.target;
    const reader = new FileReader();
    const preview = document.getElementById("preview");

    reader.onload = function () {
        preview.src = reader.result;
        preview.style.display = 'block';
    }

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>