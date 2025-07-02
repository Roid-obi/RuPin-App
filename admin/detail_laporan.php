<?php
require "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);

// Ambil informasi laporan
$laporan = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM laporan_pembayaran WHERE id = $id"));

// Ambil detail pembayaran
$sql = "
    SELECT p.*
    FROM laporan_pembayaran_detail d
    JOIN pembayaran p ON p.pembayaran_id = d.pembayaran_id
    WHERE d.laporan_id = $id
";
$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Laporan Bulan <?= $laporan['bulan'] . ' ' . $laporan['tahun'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>Detail Laporan Bulanan: <?= $laporan['bulan'] ?> <?= $laporan['tahun'] ?></h3>
    <p>Total: Rp <?= number_format($laporan['total'], 0, ',', '.') ?> | Jumlah Transaksi: <?= $laporan['jumlah_transaksi'] ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Pembayaran</th>
                <th>Metode</th>
                <th>Jumlah</th>
                <th>Tanggal Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $row['pembayaran_id'] ?></td>
                <td><?= $row['metode'] ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['tanggal_bayar'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="laporan_transaksi.php" class="btn btn-secondary">Kembali</a>
</body>
</html>
