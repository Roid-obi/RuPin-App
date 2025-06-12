<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

$item_id = $_GET['id'];
$sql = "SELECT * FROM items WHERE item_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    die("Item tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Ruang / Alat</title>
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
    <h2>Detail Ruang / Alat</h2>

    <div class="card shadow-sm mb-4">
        <div class="row g-0">
            <div class="col-md-4">
                <?php if (!empty($item['gambar'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($item['gambar']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($item['nama']) ?>">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light h-100" style="min-height: 200px;">
                        <span class="text-muted">Gambar tidak tersedia</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                    <p class="card-text"><strong>Tipe:</strong> <?= htmlspecialchars($item['tipe']) ?></p>
                    <p class="card-text"><strong>Lokasi:</strong> <?= htmlspecialchars($item['lokasi']) ?></p>
                    <p class="card-text"><strong>Harga Sewa:</strong> Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?></p>
                    <p class="card-text"><strong>Status:</strong> 
                        <span class="badge <?= $item['status'] === 'tersedia' ? 'bg-success' : 'bg-danger' ?>">
                            <?= htmlspecialchars($item['status']) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">Pesan Sekarang</button>
    <a href="cari_item.php" class="btn btn-secondary">Kembali</a>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin memesan <strong><?= htmlspecialchars($item['nama']) ?></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="pesan_item.php">
                        <input type="hidden" name="item_id" value="<?= $item_id ?>">
                        <button type="submit" class="btn btn-success">Ya, Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>