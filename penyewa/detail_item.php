<?php
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Ruang / Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <style>
        :root {
            --primary-color: #675DFE;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #594ddc;
            border-color: #594ddc;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .card-title {
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            margin-top: 3rem;
            text-align: center;
            color: #777;
        }

        .full-height-image {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="../index.php">Rupin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Jika belum login -->
        <div class="d-flex gap-2">
          <a href="../auth/login.php" class="btn btn-primary">Login</a>
          <a href="../auth/register.php" class="btn btn-outline-secondary">Register</a>
        </div>
      <?php else: ?>
        <!-- Jika sudah login -->
        <div class="d-flex align-items-center gap-3">
          <div class="text-dark text-end">
            <small class="d-block">Halo, <?= ucfirst($_SESSION['role']) ?></small>
          </div>

          <!-- Dashboard berdasarkan role -->
          <a href="
            <?php 
              switch ($_SESSION['role']) {
                  case 'penyewa': echo 'index.php'; break;
                  case 'penyedia': echo '../penyedia/index.php'; break;
                  case 'admin': echo '../admin/index.php'; break;
                  default: echo '#';
              } 
            ?>
          " class="btn btn-primary">Dashboard</a>
          
          <!-- Tombol Cari Item hanya untuk Penyewa -->
          <?php if ($_SESSION['role'] === 'penyewa'): ?>
            <a href="cari_item.php" class="btn btn-outline-primary">Cari Item</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Konten Utama -->
<div class="container py-5">
    <h2 class="text-center mb-4 text-primary">Detail Ruang / Alat</h2>

    <div class="row align-items-start g-4">
        <!-- Kolom Gambar -->
        <div class="col-md-5 d-flex">
            <img src="<?= !empty($item['gambar']) ? '../uploads/' . htmlspecialchars($item['gambar']) : '../assets/default.jpg' ?>"
                 alt="<?= htmlspecialchars($item['nama']) ?>"
                 class="full-height-image rounded">

            <!-- Alternatif jika ingin pakai div sebagai container -->
            <!-- <div class="w-100 bg-light d-flex align-items-center justify-content-center text-muted rounded"
                 style="background-image: url('../uploads/<?= htmlspecialchars($item['gambar']) ?>'); background-size: cover; background-position: center;"></div> -->
        </div>

        <!-- Kolom Card Informasi -->
        <div class="col-md-7">
            <div class="card shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                    <p class="card-text"><strong>Tipe:</strong> <?= htmlspecialchars($item['tipe']) ?></p>
                    <p class="card-text"><strong>Lokasi:</strong> <?= htmlspecialchars($item['lokasi']) ?></p>
                    <p class="card-text"><strong>Harga Sewa:</strong> Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?>/hari</p>
                    <p class="card-text"><strong>Status:</strong> 
                        <span class="badge <?= $item['status'] === 'tersedia' ? 'bg-success' : 'bg-danger' ?>">
                            <?= htmlspecialchars($item['status']) ?>
                        </span>
                    </p>
                </div>

                <!-- Tombol Aksi -->
                <div class="card-footer bg-white border-0 d-grid gap-2 d-md-flex">
                    <button class="btn btn-primary flex-fill" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">Pesan Sekarang</button>
                    <a href="cari_item.php" class="btn btn-outline-secondary flex-fill">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Footer -->
<footer>
  <div class="container">
    <p>&copy; <?= date('Y') ?> Rupin. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>