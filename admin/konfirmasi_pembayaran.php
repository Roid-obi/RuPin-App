<?php
include('../session.php');
include('../config.php');

// Pastikan user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data pembayaran dan status booking
$sql = "
    SELECT p.pembayaran_id, p.booking_id, p.jumlah, p.status AS status_bayar, p.tanggal_bayar, b.status AS status_booking
    FROM pembayaran p
    JOIN booking b ON p.booking_id = b.booking_id
    ORDER BY p.tanggal_bayar DESC
";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css"  rel="stylesheet">
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
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="konfirmasi_pembayaran.php" class="<?= basename($_SERVER['PHP_SELF']) == 'konfirmasi_pembayaran.php' ? 'active' : '' ?>">Konfirmasi Pembayaran</a>
    <a href="kelola_user.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kelola_user.php' ? 'active' : '' ?>">Kelola User</a>
    <a href="laporan_transaksi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan_transaksi.php' ? 'active' : '' ?>">Laporan Transaksi</a>
    <a href="hitung_denda.php">Hitung Denda</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <div class="top-nav rounded shadow-sm mb-4">
        <div>
            <a href="../index.php" class="go-home btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Homepage</a>
        </div>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>

    <h2>Konfirmasi Pembayaran</h2>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID Pembayaran</th>
                    <th>ID Booking</th>
                    <th>Jumlah</th>
                    <th>Status Booking</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Dikonfirmasi</th>
                    <th>Bukti Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['pembayaran_id']) ?></td>
                    <td><?= htmlspecialchars($row['booking_id']) ?></td>
                    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['status_booking']) ?></td>

                    <!-- Status Pembayaran -->
                    <td>
                        <?php if ($row['status_bayar'] === 'lunas'): ?>
                            <span class="badge bg-success">Lunas</span>
                        <?php elseif ($row['status_bayar'] === 'menunggu'): ?>
                            <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                        <?php elseif ($row['status_bayar'] === 'belum bayar'): ?>
                            <span class="badge bg-secondary">Belum Bayar</span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <td><?= $row['tanggal_bayar'] ?? '-' ?></td>

                    <!-- Bukti Pembayaran -->
                    <td>
                        <?php
                        $pembayaran_id = $row['pembayaran_id'];
                        $bukti_q = $con->query("SELECT bukti FROM pembayaran WHERE pembayaran_id = $pembayaran_id");
                        $bukti = $bukti_q->fetch_assoc()['bukti'] ?? null;

                        if ($bukti) {
                            echo "<a href='../uploads/" . htmlspecialchars($bukti) . "' target='_blank' class='btn btn-sm btn-outline-primary'>Lihat Bukti</a>";
                        } else {
                            echo "<span class='text-muted'>Belum Ada</span>";
                        }
                        ?>
                    </td>

                    <!-- Tombol Aksi -->
                    <td>
                        <?php if ($row['status_booking'] === 'ditolak') { ?>
                            <span class="badge bg-secondary">Tidak Berlaku</span>
                        <?php } elseif ($row['status_bayar'] === 'menunggu' && $bukti) { ?>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#konfirmasiModal<?= $row['pembayaran_id'] ?>">
                                Konfirmasi
                            </button>
                        <?php } elseif ($row['status_booking'] === 'menunggu') { ?>
                            <button class="btn btn-sm btn-warning" disabled>Menunggu...</button>
                        <?php } elseif ($row['status_bayar'] === 'belum bayar') { ?>
                            <span class="text-muted">-</span>
                        <?php } else { ?>
                            <span class="badge bg-success">Sudah Dikonfirmasi</span>
                        <?php } ?>
                    </td>
                </tr>

                <!-- Modal Konfirmasi -->
                <div class="modal fade" id="konfirmasiModal<?= $row['pembayaran_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                Yakin ingin mengonfirmasi pembayaran ID: <strong><?= $row['pembayaran_id'] ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a href="verifikasi_pembayaran.php?id=<?= $row['pembayaran_id'] ?>&aksi=konfirmasi" class="btn btn-success">Ya, Konfirmasi</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Bukti Pembayaran -->
<div class="modal fade" id="buktiModal<?= $row['pembayaran_id'] ?>" tabindex="-1" aria-labelledby="buktiModalLabel<?= $row['pembayaran_id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Bukti Pembayaran - ID <?= $row['pembayaran_id'] ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body text-center">
            <img src="../uploads/<?= htmlspecialchars($buktiRow['bukti']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded shadow-sm" style="max-height: 500px;">
        </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
