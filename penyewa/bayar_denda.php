<?php
include('../session.php');
include('../config.php');

// Pastikan user login sebagai penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    echo "❌ Booking ID tidak ditemukan.";
    exit;
}

$booking_id = intval($_GET['booking_id']);

// Ambil informasi denda dan pengembalian
$sql = "SELECT g.denda, g.status, g.bukti, b.booking_id, i.nama AS nama_barang
        FROM pengembalian g
        JOIN booking b ON b.booking_id = g.booking_id
        JOIN items i ON i.item_id = b.item_id
        WHERE g.booking_id = ? AND b.user_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Data pengembalian tidak ditemukan atau bukan milikmu.";
    exit;
}

$data = $result->fetch_assoc();
$denda = $data['denda'];
$status_kembali = $data['status'];
$nama_barang = $data['nama_barang'];
$telah_dibayar = ($denda == 0);

// Template WhatsApp
$no_admin = "62859131332583"; // Ganti dengan nomor admin sebenarnya
$pesan_wa = urlencode("Halo Admin, saya ingin melakukan pembayaran denda keterlambatan.\n\nDetail:\nID Booking: #$booking_id\nNama Barang: $nama_barang\nJumlah Denda: Rp " . number_format($denda, 0, ',', '.') . "\n\nMohon informasinya terkait metode pembayarannya. Terima kasih.");
$link_wa = "https://wa.me/$no_admin?text=$pesan_wa";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bayar Denda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Pembayaran Denda</h4>
        </div>
        <div class="card-body">
            <p><strong>Nama Barang:</strong> <?= htmlspecialchars($nama_barang) ?></p>
            <p><strong>Status Pengembalian:</strong> <?= htmlspecialchars($status_kembali) ?></p>
            <p><strong>ID Booking:</strong> #<?= $booking_id ?></p>
            <p><strong>Jumlah Denda:</strong> Rp <?= number_format($denda, 0, ',', '.') ?> <small class="text-muted">(10% dari total pembayaran)</small></p>

            <?php if ($telah_dibayar): ?>
                <div class="alert alert-success">✅ Denda telah dibayar atau tidak ada denda yang harus dibayarkan.</div>
                <a href="status_pemesanan.php" class="btn btn-secondary">Kembali</a>
            <?php else: ?>
                <div class="alert alert-info">
                    Silakan hubungi admin melalui WhatsApp untuk melakukan pembayaran denda.
                    <br>Denda ini adalah <strong>10%</strong> dari total pembayaran karena pengembalian terlambat.
                </div>
                <a href="<?= $link_wa ?>" class="btn btn-success" target="_blank">
                    <i class="fa-brands fa-whatsapp me-2"></i>Hubungi Admin via WhatsApp
                </a>
                <a href="status_pemesanan.php" class="btn btn-secondary ms-2">Kembali</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
