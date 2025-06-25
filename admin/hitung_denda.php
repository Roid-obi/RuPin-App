<?php
session_start();

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hitung Denda Keterlambatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php">Dashboard</a>
    <a href="konfirmasi_pembayaran.php">Konfirmasi Pembayaran</a>
    <a href="kelola_user.php">Kelola User</a>
    <a href="laporan_transaksi.php">Laporan Transaksi</a>
    <a href="hitung_denda.php" class="active">Hitung Denda</a>
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
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small><br>
            <!-- <a href="../logout.php" class="text-danger text-decoration-none btn btn-link btn-sm">Logout</a> -->
        </div>
    </div>

    <h2>Hitung Denda Keterlambatan</h2>
    <p>Masukkan ID pemesanan dan jumlah hari keterlambatan untuk menghitung dendanya.</p>

    <form id="form-denda" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="booking_id" class="form-label">ID Pemesanan</label>
                <input type="text" class="form-control" id="booking_id" name="booking_id" placeholder="Contoh: 123" required>
            </div>
            <div class="col-md-4">
                <label for="hari_keterlambatan" class="form-label">Jumlah Hari Terlambat</label>
                <input type="number" min="0" class="form-control" id="hari_keterlambatan" name="hari_keterlambatan" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Hitung Denda
                </button>
            </div>
        </div>
    </form>

    <!-- Hasil Perhitungan -->
    <div id="hasil-denda" class="d-none">
        <h4>Detail Denda</h4>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>ID Pemesanan:</strong> <span id="info-booking-id"></span></li>
            <li class="list-group-item"><strong>Nama Penyewa:</strong> <span id="info-nama-penyewa"></span></li>
            <li class="list-group-item"><strong>Jenis Item:</strong> <span id="info-jenis-item"></span></li>
            <li class="list-group-item"><strong>Harga Sewa/hari:</strong> Rp<span id="info-harga-sewa"></span></li>
            <li class="list-group-item"><strong>Keterlambatan:</strong> <span id="info-hari"></span> hari</li>
            <li class="list-group-item fw-bold fs-5 text-success">
                <strong>Total Denda:</strong> Rp<span id="info-total-denda"></span>
            </li>
        </ul>
    </div>
</div>

<script>
document.getElementById('form-denda').addEventListener('submit', function(e) {
    e.preventDefault();

    const bookingId = document.getElementById('booking_id').value.trim();
    const hariKeterlambatan = parseInt(document.getElementById('hari_keterlambatan').value);

    if (isNaN(hariKeterlambatan) || hariKeterlambatan < 0) {
        alert("Masukkan jumlah hari keterlambatan yang valid.");
        return;
    }

    fetch('proses_hitung_denda.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            booking_id: bookingId,
            hari_keterlambatan: hariKeterlambatan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        document.getElementById('info-booking-id').textContent = data.booking_id;
        document.getElementById('info-nama-penyewa').textContent = data.nama_penyewa;
        document.getElementById('info-jenis-item').textContent = data.jenis_item;
        document.getElementById('info-harga-sewa').textContent = parseFloat(data.harga_sewa).toLocaleString('id-ID');
        document.getElementById('info-hari').textContent = hariKeterlambatan;
        document.getElementById('info-total-denda').textContent = parseFloat(data.total_denda).toLocaleString('id-ID');

        document.getElementById('hasil-denda').classList.remove('d-none');
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Terjadi kesalahan saat mengambil data.");
    });
});
</script>

</body>
</html>