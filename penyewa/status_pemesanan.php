<?php
// session_start();
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT p.booking_id, i.nama, p.status AS status_pesan, b.status AS status_bayar
        FROM pemesanan p
        JOIN items i ON i.item_id = p.item_id
        LEFT JOIN pembayaran b ON b.booking_id = p.booking_id
        WHERE p.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pemesanan</title>
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
    <a href="cari_item.php">Cari Item</a>
    <a href="status_pemesanan.php" class="active">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h2>Status Pemesanan</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nama Barang</th>
                    <th>Status Pemesanan</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['status_pesan']) ?></td>
                    <td><?= $row['status_bayar'] ? htmlspecialchars($row['status_bayar']) : '-' ?></td>
                    <td>
                        <?php if ($row['status_pesan'] === 'menunggu') { ?>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#batalModal<?= $row['booking_id'] ?>">Batalkan</button>
                        <?php } else { ?>
                            <span class="text-muted">Tidak tersedia</span>
                        <?php } ?>
                        <a href="detail_pembayaran.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-info text-white">Detail Pesanan</a>
                    </td>
                </tr>

                <!-- Modal Konfirmasi Batal -->
                <div class="modal fade" id="batalModal<?= $row['booking_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Pembatalan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin membatalkan pesanan untuk barang: <strong><?= htmlspecialchars($row['nama']) ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <a href="batal_pemesanan.php?id=<?= $row['booking_id'] ?>" class="btn btn-danger">Ya, Batalkan</a>
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