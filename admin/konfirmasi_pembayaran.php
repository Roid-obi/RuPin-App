<?php
include('../session.php');
include('../config.php');

$sql = "SELECT * FROM pembayaran ORDER BY tanggal_bayar DESC";
$result = $con->query($sql);
?>

<h2>Konfirmasi Pembayaran</h2>
<table border="1">
    <tr>
        <th>ID Pembayaran</th>
        <th>ID Pemesanan</th>
        <th>Jumlah</th>
        <th>Status</th>
        <th>Tanggal Bayar</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['pembayaran_id'] ?></td>
        <td><?= $row['booking_id'] ?></td>
        <td><?= $row['jumlah'] ?></td>
        <td><?= $row['status'] ?></td>
        <td><?= $row['tanggal_bayar'] ?></td>
        <td>
            <?php if ($row['status'] == 'menunggu') { ?>
            <a href="verifikasi_pembayaran.php?id=<?= $row['pembayaran_id'] ?>&aksi=konfirmasi">Konfirmasi</a>
            <?php } else { echo '-'; } ?>
        </td>
    </tr>
    <?php } ?>
</table>
