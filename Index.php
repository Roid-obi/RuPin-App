<?php
session_start(); // Tambahkan ini di awal file
include './config.php';

// Ambil item dari database
$items = mysqli_query($con, "SELECT * FROM items WHERE status = 'tersedia' LIMIT 6");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rupin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="./styles/homepage.css" rel="stylesheet">
  <style>
    /* Blok warna untuk ganti gambar */
    .image-placeholder {
      height: 200px;
      background-color: #f8f9fa; /* Warna default */
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: bold;
      color: #6c757d;
    }

    /* Warna-warna spesifik untuk setiap blok */
    .image-placeholder.room {
      background-color: #6f42c1;
      color: white;
    }

    .image-placeholder.equipment {
      background-color: #28a745;
      color: white;
    }

    .image-placeholder.banner {
      background-color: #007bff;
      color: white;
    }

    .image-placeholder.gallery {
      background-color: #ffc107;
      color: white;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <!-- <a class="navbar-brand fw-bold " href="index.php">Rupin</a> -->
    <a class="navbar-brand fw-bold text-primary" href="./index.php"><img class="navbar-logo" src="../image/logo-rupin.png" alt="logo-rupin"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Jika belum login -->
        <div class="d-flex gap-2">
          <a href="./auth/login.php" class="btn btn-primary">Login</a>
          <a href="./auth/register.php" class="btn btn-outline-secondary">Register</a>
        </div>
      <?php else: ?>
        <!-- Jika sudah login -->
        <div class="d-flex align-items-center gap-3">
          <div class="text-dark text-end">
            <small class="d-block">Halo, <?= ucfirst($_SESSION['role']) ?></small>
          </div>

          <!-- Tombol Dashboard berdasarkan role -->
          <a href="
            <?php 
              switch ($_SESSION['role']) {
                  case 'penyewa': echo 'penyewa/index.php'; break;
                  case 'penyedia': echo 'penyedia/index.php'; break;
                  case 'admin': echo 'admin/index.php'; break;
                  default: echo '#';
              } 
            ?>
          " class="btn btn-primary">Dashboard</a>

          <!-- Tombol Cari Item hanya untuk Penyewa -->
          <?php if ($_SESSION['role'] === 'penyewa'): ?>
            <a href="./penyewa/cari_item.php" class="btn btn-outline-primary">Cari Item</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div class="hero-content container">
    <h1 class="display-4 fw-bold">Sewa Ruangan & Alat Mudah</h1>
    <p class="lead mb-4">Temukan berbagai kebutuhan sewa dengan cepat dan nyaman.</p>
    <a href="#items" class="btn btn-primary btn-lg">Lihat Item</a>
  </div>
</section>




<!-- Tentang -->
<section class="py-5 bg-light text-center my-5">
  <div class="container">
    <h2 class="head-homepage mb-4">Tentang RuPin</h2>
    <p class="lead mb-3">
      RuPin adalah platform digital yang memudahkan Anda untuk menyewa ruangan maupun alat secara online.
    </p>
    <p class="mb-0">
      Dengan tampilan yang sederhana dan fitur yang lengkap, kami hadir untuk memberikan pengalaman sewa menyewa yang lebih cepat, aman, dan nyaman.
    </p>
  </div>
</section>

<!-- Card Items -->
<section id="items" class="container py-5">
  <h2 class="text-center mb-4 head-homepage">Tersedia Untuk Disewa</h2>
  <?php if (mysqli_num_rows($items) > 0): ?>
    <div class="row g-4">
      <?php while($item = mysqli_fetch_assoc($items)) : ?>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <?php if (!empty($item['gambar'])) : ?>
              <img src="uploads/<?= htmlspecialchars($item['gambar']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Gambar">
            <?php else: ?>
              <img src="assets/default.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Default">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($item['tipe']) ?> - Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?>/hari</p>
              <p class="text-muted mb-0"><small><?= htmlspecialchars($item['lokasi']) ?></small></p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="text-center text-muted">
      <p class="fs-5">Item belum tersedia.</p>
    </div>
  <?php endif; ?>
</section>


<!-- Pemesanan Aktif -->
<section class="container py-5">
  <h2 class="text-center mb-4 head-homepage">Sedang Disewa</h2>

  <?php
  // Ambil 10 pemesanan terakhir yang statusnya 'disewa'
  $query_disewa = "
    SELECT p.booking_id, i.nama AS item_nama, u.nama AS penyewa_nama, p.tanggal
    FROM pemesanan p
    JOIN items i ON p.item_id = i.item_id
    JOIN users u ON p.user_id = u.user_id
    WHERE p.status = 'disewa'
    ORDER BY p.tanggal DESC
    LIMIT 10
  ";
  $result_disewa = mysqli_query($con, $query_disewa);
  ?>

  <?php if (mysqli_num_rows($result_disewa) > 0): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Nama Item</th>
            <th>Penyewa</th>
            <th>Tanggal Sewa</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = mysqli_fetch_assoc($result_disewa)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['item_nama']) ?></td>
              <td><?= htmlspecialchars($row['penyewa_nama']) ?></td>
              <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text-center text-muted">
      <p class="fs-5">Belum ada pemesanan yang sedang berjalan.</p>
    </div>
  <?php endif; ?>
</section>


<!-- Footer -->
<footer>
  <div class="container">
    <p>&copy; <?= date('Y') ?> Rupin. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>