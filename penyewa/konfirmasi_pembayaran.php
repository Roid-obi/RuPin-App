<?php
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

$sql = "SELECT 
            p.jumlah, p.metode, p.bukti, 
            b.jumlah_hari, 
            i.nama AS nama_item, i.harga_sewa
        FROM pembayaran p
        JOIN booking b ON p.booking_id = b.booking_id
        JOIN items i ON b.item_id = i.item_id
        WHERE p.booking_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data pembayaran tidak ditemukan.");
}

$data = $result->fetch_assoc();

$harga_sewa_per_hari = $data['harga_sewa'];
$jumlah_hari = $data['jumlah_hari'];
$total_sewa = $harga_sewa_per_hari * $jumlah_hari;
$biaya_admin = $total_sewa * 0.05;
$total_pembayaran = $total_sewa + $biaya_admin;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php">Dashboard</a>
    <a href="status_pemesanan.php">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h2 class="mb-4">Konfirmasi Pembayaran</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p><strong>Item:</strong> <?= htmlspecialchars($data['nama_item']) ?></p>
            <p><strong>Jumlah Hari Sewa:</strong> <?= $jumlah_hari ?> hari</p>
            <p><strong>Harga Sewa per Hari:</strong> Rp <?= number_format($harga_sewa_per_hari, 0, ',', '.') ?></p>
            <p><strong>Subtotal:</strong> Rp <?= number_format($total_sewa, 0, ',', '.') ?></p>
            <p><strong>Biaya Admin (5%):</strong> Rp <?= number_format($biaya_admin, 0, ',', '.') ?></p>
            <h5><strong>Total yang Harus Dibayar:</strong> Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></h5>
            <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($data['metode']) ?></p>
        </div>
    </div>

    <!-- QR Code -->
    <div class="text-center my-4">
        <img src="../image/qr.png" alt="QR Code Pembayaran" class="img-fluid rounded shadow-sm" style="max-width: 250px;">
        <p class="mt-2"><strong>Scan QR Code untuk melakukan pembayaran ke rekening admin.</strong></p>
    </div>

    <!-- Upload Bukti Pembayaran -->
    <?php if ($data['bukti'] === null) { ?>
        <div class="card p-4 shadow-sm mb-4">
            <form action="upload_bukti.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
                <div class="mb-3">
                    <label for="bukti" class="form-label">Upload Bukti Pembayaran:</label>
                    <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-upload me-2"></i>Upload Bukti
                </button>
            </form>
        </div>
    <?php } else { ?>
        <div class="alert alert-success text-center">
            <strong>Bukti sudah dikirim:</strong><br>
            <img src="../uploads/<?= htmlspecialchars($data['bukti']) ?>" alt="Bukti" class="img-fluid rounded mt-2" style="max-height: 250px;">
        </div>
    <?php } ?>

    <!-- WA Admin -->
    <div class="alert alert-info text-center mb-4">
        <p><strong>Ingin cepat diproses?</strong> Kirim bukti pembayaran melalui WhatsApp ke admin.</p>
        <a href="https://wa.me/6281234567890?text=Halo%20admin,%20saya%20ingin%20mengirim%20bukti%20pembayaran%20untuk%20booking%20ID:<?= $booking_id ?>" 
           target="_blank" class="btn btn-success">
           <i class="fab fa-whatsapp me-2"></i>Kirim Bukti via WhatsApp
        </a>
    </div>

    <a href="status_pemesanan.php" class="btn btn-primary">Lihat Status Pemesanan</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
