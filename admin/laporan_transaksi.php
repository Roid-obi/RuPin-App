<?php
include('../session.php');
include('../config.php');

$sql = "SELECT MONTH(tanggal_bayar) as bulan, SUM(jumlah) as total
        FROM pembayaran
        WHERE status = 'dikonfirmasi'
        GROUP BY MONTH(tanggal_bayar)";
$result = $con->query($sql);
?>

<h2>Laporan Transaksi Bulanan</h2>
<table border="1">
    <tr>
        <th>Bulan</th>
        <th>Total Transaksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= date("F", mktime(0,0,0,$row['bulan'], 1)) ?></td>
        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
    </tr>
    <?php } ?>
</table>
