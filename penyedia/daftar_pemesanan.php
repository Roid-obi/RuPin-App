<?php
include '../session.php';
include '../config.php';

// Pastikan user adalah penyedia
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyedia') {
    header("Location: ../auth/login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$sql = "
SELECT b.booking_id, i.nama AS item, u.nama AS penyewa, b.status, b.tanggal 
FROM booking b
JOIN items i ON b.item_id = i.item_id
JOIN users u ON b.user_id = u.user_id
WHERE i.user_id = ?
ORDER BY b.tanggal DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pemesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .badge-menunggu { background-color: #ffc107; color: #212529; }
        .badge-disetujui { background-color: #198754; }
        .badge-ditolak { background-color: #dc3545; }
        .badge-selesai { background-color: #0d6efd; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="daftar_pemesanan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'daftar_pemesanan.php' ? 'active' : '' ?>">Daftar Pemesanan</a>
    <a href="kelola_item.php" class="<?= basename($_SERVER['PHP_SELF']) == 'kelola_item.php' ? 'active' : '' ?>">Kelola Item</a>
    <a href="laporan_keterlambatan.php">Lapor Keterlambatan</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <div class="top-nav rounded shadow-sm mb-4 d-flex justify-content-between align-items-center">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Homepage</a>
        <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
    </div>

    <h2>Daftar Pemesanan</h2>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Item</th>
                    <th>Penyewa</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res->num_rows > 0): ?>
                    <?php while ($r = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['booking_id']) ?></td>
                            <td><?= htmlspecialchars($r['item']) ?></td>
                            <td><?= htmlspecialchars($r['penyewa']) ?></td>
                            <td><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td>
                                <span class="badge-status badge-<?= str_replace(' ', '', strtolower($r['status'])) ?>">
                                    <?= ucfirst($r['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'menunggu'): ?>
                                    <a href="verifikasi_pesanan.php?id=<?= $r['booking_id'] ?>&aksi=terima" class="btn btn-sm btn-success">Terima</a>
                                    <a href="verifikasi_pesanan.php?id=<?= $r['booking_id'] ?>&aksi=tolak" class="btn btn-sm btn-danger">Tolak</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada pemesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
