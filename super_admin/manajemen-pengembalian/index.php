<?php
session_start();
include('../../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super-admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Handle upload bukti pengembalian dan simpan data jika ada form post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status = $_POST['status'];
    $denda = $_POST['denda'];

    $bukti = null;
    if (!empty($_FILES['bukti']['name'])) {
        $target_dir = "../../uploads/";
        $bukti = basename($_FILES['bukti']['name']);
        $target_file = $target_dir . $bukti;
        move_uploaded_file($_FILES['bukti']['tmp_name'], $target_file);
    }

    $stmt = $con->prepare("INSERT INTO pengembalian (booking_id, tanggal_kembali, status, denda, bukti) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $booking_id, $tanggal_kembali, $status, $denda, $bukti);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $con->query("DELETE FROM pengembalian WHERE pengembalian_id = $id");
    header("Location: index.php");
    exit;
}

$result = $con->query("SELECT * FROM pengembalian ORDER BY tanggal_kembali DESC");
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
    <a href="../manajemen-laporan-pembayaran/index.php">Manajemen laporan Pembayaran</a>
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

    <h2>Manajemen Pengembalian</h2>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="row g-2">
            <div class="col-md-2">
                <input type="number" name="booking_id" class="form-control" placeholder="Booking ID" required>
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_kembali" class="form-control" required>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control" required>
                    <option value="tepat waktu">Tepat Waktu</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="hilang/rusak">Hilang/Rusak</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="denda" step="0.01" class="form-control" placeholder="Denda">
            </div>
            <div class="col-md-2">
                <input type="file" name="bukti" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tambah</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Booking ID</th>
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
                        <td><?= $row['tanggal_kembali'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>Rp <?= number_format($row['denda'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($row['bukti']) : ?>
                                <a href="../../uploads/<?= $row['bukti'] ?>" target="_blank">Lihat Bukti</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?delete=<?= $row['pengembalian_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
