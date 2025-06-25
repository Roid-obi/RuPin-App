<?php
session_start();

// Pastikan hanya penyedia yang bisa akses
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Koneksi ke database
require "../config.php"; // Sesuaikan path dengan struktur folder kamu

$user_id = $_SESSION['user_id'];

// Ambil detail user dari database
$sql = "SELECT nama, role FROM users WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User tidak ditemukan.");
}

$user = $result->fetch_assoc();

// Cek apakah role-nya penyedia
if ($user['role'] !== 'penyedia') {
    header("Location: ../auth/login.php");
    exit;
}

$penyedia_nama = ucfirst($user['nama']);
$nomor_admin = "62859131332583"; // Nomor admin tanpa tanda +
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lapor Keterlambatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php">Dashboard</a>
    <a href="daftar_pemesanan.php">Daftar Pemesanan</a>
    <a href="kelola_item.php">Kelola Item</a>
    <a href="laporan_keterlambatan.php" class="active">Lapor Keterlambatan</a>
    <a href="profil.php">Profil Saya</a>
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
            <small>Halo, <?= $penyedia_nama ?></small><br>
            <!-- <a href="../logout.php" class="text-danger text-decoration-none btn btn-link btn-sm">Logout</a> -->
        </div>
    </div>

    <h2>Lapor Keterlambatan Pengembalian</h2>
    <p>Silakan masukkan ID pemesanan dan jumlah hari keterlambatan.</p>

    <form id="form-laporan">
        <div class="mb-3">
            <label for="booking_id" class="form-label">ID Pemesanan</label>
            <input type="text" class="form-control" id="booking_id" name="booking_id" placeholder="Contoh: 123" required>
        </div>
        <div class="mb-3">
            <label for="hari_keterlambatan" class="form-label">Jumlah Hari Terlambat</label>
            <input type="number" min="0" class="form-control" id="hari_keterlambatan" name="hari_keterlambatan" placeholder="Contoh: 2" required>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fab fa-whatsapp me-2"></i>Kirim Laporan ke Admin
        </button>
    </form>

    <!-- Preview Pesan -->
    <div id="preview-message" class="mt-4 alert alert-info d-none">
        <strong>Pesan yang akan dikirim:</strong><br>
        <pre id="pesan-whatsapp" class="mb-0"></pre>
    </div>
</div>

<script>
document.getElementById('form-laporan').addEventListener('submit', function(e) {
    e.preventDefault();

    const bookingId = document.getElementById('booking_id').value.trim();
    const hariKeterlambatan = document.getElementById('hari_keterlambatan').value.trim();
    const penyediaNama = "<?= $penyedia_nama ?>";
    const waktu = new Date().toLocaleString('id-ID');

    const pesan = `⚠️ LAPORAN KETERLAMBATAN\n\nID Pemesanan: ${bookingId}\nPenyedia: ${penyediaNama}\nHari Terlambat: ${hariKeterlambatan} hari\nWaktu Laporan: ${waktu}`;

    document.getElementById('pesan-whatsapp').textContent = pesan;
    document.getElementById('preview-message').classList.remove('d-none');

    // Buat URL WhatsApp
    const encodedPesan = encodeURIComponent(pesan);
    const whatsappURL = `https://wa.me/<?= $nomor_admin ?>?text=${encodedPesan}`;

    // Redirect ke WhatsApp
    window.open(whatsappURL, '_blank');
});
</script>

</body>
</html>