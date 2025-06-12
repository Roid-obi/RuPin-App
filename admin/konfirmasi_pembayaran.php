<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$sql = "
    SELECT p.pembayaran_id, p.booking_id, p.jumlah, p.status AS status_bayar, p.tanggal_bayar, pm.status AS status_pesan
    FROM pembayaran p
    JOIN pemesanan pm ON p.booking_id = pm.booking_id
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
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #675DFE;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
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

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #ddd;
            margin-bottom: 2rem;
            height: 70px;
        }

        .btn-outline-primary {
            color: #594ddc;
            border-color: #594ddc;
        }

        .content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center py-3">Rupin - Admin</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="konfirmasi_pembayaran.php" class="<?= basename($_SERVER['PHP_SELF']) == 'konfirmasi_pembayaran.php' ? 'active' : '' ?>">Konfirmasi Pembayaran</a>
    <a href="kelola_user.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kelola_user.php' ? 'active' : '' ?>">Kelola User</a>
    <a href="laporan_transaksi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan_transaksi.php' ? 'active' : '' ?>">Laporan Transaksi</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <!-- Navbar Atas di Dalam Konten -->
        <div class="top-nav rounded shadow-sm mb-4">
            <div>
                <a href="../index.php" class="btn btn-outline-primary btn-sm">← Ke Homepage</a>
            </div>
            <div class="text-end">
                <small>Halo, <?= ucfirst($_SESSION['role']) ?></small><br>
                <!-- <a href="../logout.php" class="text-danger text-decoration-none btn btn-link btn-sm">Logout</a> -->
            </div>
        </div>

    <h2>Konfirmasi Pembayaran</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Pembayaran</th>
                    <th>ID Pemesanan</th>
                    <th>Jumlah</th>
                    <th>Status Pesanan</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['pembayaran_id']) ?></td>
                    <td><?= htmlspecialchars($row['booking_id']) ?></td>
                    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['status_pesan']) ?></td>
                    <td><?= htmlspecialchars($row['status_bayar']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                    <td>
                        <?php if ($row['status_pesan'] === 'ditolak') { ?>
                            <!-- Jika pesanan ditolak, pembayaran otomatis tidak dikonfirmasi -->
                            <span class="badge bg-secondary">Tidak Berlaku</span>
                        <?php } elseif ($row['status_pesan'] === 'menunggu') { ?>
                            <!-- Jika pesanan belum diterima -->
                            <button class="btn btn-sm btn-warning" disabled>Menunggu...</button>
                        <?php } elseif ($row['status_bayar'] === 'menunggu') { ?>
                            <!-- Jika pesanan diterima tapi pembayaran belum dikonfirmasi -->
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#konfirmasiModal<?= $row['pembayaran_id'] ?>">
                                Konfirmasi
                            </button>
                        <?php } else { ?>
                            <!-- Jika sudah dikonfirmasi -->
                            <span class="badge bg-success">Sudah Dikonfirmasi</span>
                        <?php } ?>
                    </td>
                </tr>

                <!-- Modal Konfirmasi -->
                <div class="modal fade" id="konfirmasiModal<?= $row['pembayaran_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Pembayaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin mengkonfirmasi pembayaran ID: <strong><?= $row['pembayaran_id'] ?></strong>?
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>