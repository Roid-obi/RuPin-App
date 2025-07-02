<?php
session_start();
include('../../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super-admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM pengembalian WHERE pengembalian_id = $id");
    header("Location: index.php");
    exit;
}

$result = $con->query("SELECT p.*, b.item_id, i.nama as nama_item FROM pengembalian p JOIN booking b ON p.booking_id = b.booking_id JOIN items i ON b.item_id = i.item_id ORDER BY tanggal_kembali DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengembalian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../../styles/dashboard.css" rel="stylesheet">
    <style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            font-size: 0.95rem;
        }
        .custom-table thead tr {
            background-color: #f8f9fa;
            color: #495057;
        }
        .custom-table th,
        .custom-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .custom-table tbody tr:hover {
            background-color: #f1f3f5;
        }
        .preview-img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="../index.php">Dashboard</a>
    <a href="../manajemen-ruang-alat/index.php">Manajemen Ruang & alat</a>
    <a href="../manajemen-pemesanan/index.php">Manajemen Pemesanan</a>
    <a href="../manajemen-pembayaran/index.php">Manajemen Pembayaran</a>
    <a href="./index.php" class="active">Manajemen Pengembalian</a>
    <a href="../manajemen-laporan-pembayaran/index.php">Manajemen Laporan Pembayaran</a>
    <a href="../manajemen-pengguna/index.php">Manajemen Pengguna</a>
    <a href="../profil.php">Profil Saya</a>
</div>
<div class="content">
    <div class="top-nav rounded shadow-sm mb-4">
        <div>
            <a href="../index.php" class="go-home btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Homepage</a>
        </div>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manajemen Pengembalian</h2>
        <a href="tambah.php" class="btn btn-success">
            <i class="fa fa-plus me-1"></i> Tambah Pengembalian
        </a>
    </div>


    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Booking ID</th>
                    <th>Nama Item</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Denda</th>
                    <th>Bukti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['pengembalian_id'] ?></td>
                        <td><?= $row['booking_id'] ?></td>
                        <td><?= $row['nama_item'] ?></td>
                        <td><?= $row['tanggal_kembali'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>Rp <?= number_format($row['denda'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($row['bukti']) : ?>
                                <a href="../../uploads/<?= $row['bukti'] ?>" target="_blank">
                                    <img src="../../uploads/<?= $row['bukti'] ?>" alt="Bukti" class="preview-img">
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['pengembalian_id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus" data-id="<?= $row['pengembalian_id'] ?>"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="GET" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          Apakah kamu yakin ingin menghapus data pengembalian ini?
          <input type="hidden" name="delete" id="delete-id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>