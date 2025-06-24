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
  <style>
    :root {
      --primary-color: #262e54;
    }

    .navbar-logo {
      /* width: 100%; */
      height: 50px;
    }

    .navbar-brand {
      font-weight: bold;
      color: #016efe;
    }

    .hero {
      position: relative;
      background: url('./image/bg.jpg') center center/cover no-repeat;
      height: 90vh;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero::after {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover {
      background-color: #016efe;
      border-color: #016efe;
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
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <!-- <a class="navbar-brand fw-bold " href="index.php">Rupin</a> -->
     <img class="navbar-logo" src="./image/logo-rupin.png" alt="logo-rupin">
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
    <h2 class="text-primary mb-4">Tentang RuPin</h2>
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
  <h2 class="text-center mb-4 text-primary">Tersedia Untuk Disewa</h2>
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

<!-- Footer -->
<footer>
  <div class="container">
    <p>&copy; <?= date('Y') ?> Rupin. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>