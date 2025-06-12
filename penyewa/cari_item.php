<?php
include('../session.php');
include('../config.php');

$query = "SELECT * FROM items WHERE status = 'tersedia'";
$result = $con->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Ruang/Alat Tersedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
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
        }

        /* Gaya tambahan untuk card */
        .card-img-top {
            height: 200px; /* Sesuaikan tinggi gambar */
            object-fit: cover; /* Potong gambar agar proporsional */
            width: 100%; /* Pastikan penuh sesuai lebar card */
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        .btn {
            margin-top: auto;
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
    <h2>Daftar Ruang/Alat Tersedia</h2>
    <div class="row mt-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <?php if (!empty($item['gambar'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['nama']) ?>">
                        <?php else: ?>
                            <div class="text-center py-4 text-muted bg-light card-img-top">Tidak ada gambar</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                            <p class="card-text">
                                <strong>Tipe:</strong> <?= htmlspecialchars($item['tipe']) ?><br>
                                <strong>Harga Sewa:</strong> Rp<?= number_format($item['harga_sewa'], 0, ',', '.') ?><br>
                                <strong>Lokasi:</strong> <?= htmlspecialchars($item['lokasi']) ?>
                            </p>
                            <a href="detail_item.php?id=<?= $item['item_id'] ?>" class="btn btn-primary w-100">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">Belum ada ruang atau alat tersedia saat ini.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>