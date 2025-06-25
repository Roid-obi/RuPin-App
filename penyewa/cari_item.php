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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Ruang/Alat Tersedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link href="../styles/homepage.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="../index.php"><img class="navbar-logo" src="../image/logo-rupin.png" alt="logo-rupin"></a>
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
            <a href="#" class="btn btn-outline-primary disabled">Cari Item</a>
          <?php endif; ?>

        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Konten Utama -->
<div class="container py-5">
    <h2 class="text-center mb-4 head-homepage">Daftar Ruang/Alat Tersedia</h2>

    <!-- Form Pencarian -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari ruang/alat...">
        </div>
    </div>

    <div class="row g-4" id="itemContainer">
        <!-- Pesan Tidak Ditemukan -->
        <div class="col-12 text-center text-muted" id="notFoundMessage" style="display: none;">
            <p class="fs-5">Item tidak ditemukan.</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="col-md-4 item-card" data-nama="<?= strtolower(htmlspecialchars($item['nama'])) ?>">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($item['gambar'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['gambar']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($item['nama']) ?>">
                        <?php else: ?>
                            <img src="../assets/default.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Default">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                            <p class="card-text">
                                <?= htmlspecialchars($item['tipe']) ?> - Rp<?= number_format($item['harga_sewa'], 0, ',', '.') ?>/hari
                            </p>
                            <p class="text-muted mb-0"><small><?= htmlspecialchars($item['lokasi']) ?></small></p>
                            <a href="detail_item.php?id=<?= $item['item_id'] ?>" class="btn btn-primary mt-3 w-100">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">
                <p class="fs-5">Item belum tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="mt-auto">
  <div class="container">
    <p>&copy; <?= date('Y') ?> Rupin. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>   

<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const term = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.item-card');
        let found = false;

        cards.forEach(card => {
            const name = card.getAttribute('data-nama');
            if (name.includes(term)) {
                card.style.display = 'block';
                found = true;
            } else {
                card.style.display = 'none';
            }
        });

        const notFound = document.getElementById('notFoundMessage');
        if (term === '') {
            notFound.style.display = 'none';
        } else {
            notFound.style.display = found ? 'none' : 'block';
        }
    });
</script>
</body>
</html>