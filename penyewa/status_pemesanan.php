<?php
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            b.booking_id, 
            i.nama, 
            b.status AS status_pesan, 
            p.status AS status_bayar,
            g.status AS status_kembali,
            g.bukti
        FROM booking b
        JOIN items i ON i.item_id = b.item_id
        LEFT JOIN pembayaran p ON p.booking_id = b.booking_id
        LEFT JOIN pengembalian g ON g.booking_id = b.booking_id
        WHERE b.user_id = ?";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">    
    <link href="../styles/dashboard.css" rel="stylesheet">
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
    <a href="index.php">Dashboard</a>
    <a href="status_pemesanan.php" class="active">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <div class="top-nav rounded shadow-sm mb-4 d-flex justify-content-between align-items-center">
        <a href="../index.php" class="go-home btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Homepage</a>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>

    <h2>Status Pemesanan</h2>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Status Pemesanan</th>
                    <th>Status Pembayaran</th>
                    <th>Status Pengembalian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td>
                        <?php if ($row['status_pesan'] === 'menunggu'): ?>
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        <?php elseif ($row['status_pesan'] === 'dibatalkan'): ?>
                            <span class="badge bg-danger">Dibatalkan</span>
                        <?php else: ?>
                            <span class="badge bg-success">Selesai</span>
                        <?php endif; ?>
                    </td>
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

                    <td>
                        <?php if ($row['status_kembali'] === 'tepat waktu'): ?>
                            <span class="badge bg-info text-dark">Dikembalikan</span>
                        <?php elseif ($row['status_kembali'] === 'terlambat'): ?>
                            <span class="badge bg-danger">Terlambat</span>
                        <?php else: ?>
                            <span class="text-muted">Belum</span>
                        <?php endif; ?>
                    </td>
                    <td class="d-flex flex-wrap gap-2">
                        <?php if ($row['status_pesan'] === 'menunggu'): ?>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#batalModal<?= $row['booking_id'] ?>">Batalkan</button>
                        <?php endif; ?>

                        <a href="detail_pembayaran.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-info text-white">Detail Pembayaran</a>

                        <?php if ($row['status_bayar'] === 'lunas' && empty($row['status_kembali'])): ?>
                            <a href="form_pengembalian.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-success">Kembalikan</a>
                        <?php endif; ?>

                        <?php if ($row['status_kembali'] === 'terlambat'): ?>
                            <a href="bayar_denda.php?booking_id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-warning">Bayar Denda</a>
                        <?php endif; ?>
                    </td>

                </tr>

                <!-- Modal Konfirmasi Batal -->
                <div class="modal fade" id="batalModal<?= $row['booking_id'] ?>" tabindex="-1" aria-labelledby="batalLabel<?= $row['booking_id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin membatalkan pemesanan <strong><?= htmlspecialchars($row['nama']) ?></strong>?
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
