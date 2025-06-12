<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$sql = "SELECT * FROM pembayaran ORDER BY tanggal_bayar DESC";
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
            flex-direction: row;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #675DFE;
            color: white;
            position: fixed; /* Sidebar tetap di tempat */
            top: 0;
            left: 0;
            height: 100vh; /* Penuh dari atas ke bawah */
            overflow-y: auto; /* Jika isi terlalu panjang */
        }

        .content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px; /* Agar konten tidak tertutup sidebar */
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
    <h2>Konfirmasi Pembayaran</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Pembayaran</th>
                    <th>ID Pemesanan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
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
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'menunggu') { ?>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#konfirmasiModal<?= $row['pembayaran_id'] ?>">
                                Konfirmasi
                            </button>
                        <?php } else { echo '-'; } ?>
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