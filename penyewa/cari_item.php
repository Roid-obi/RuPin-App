<?php
include('../session.php');
include('../config.php');

$query = "SELECT * FROM items WHERE status = 'tersedia'";
$result = $con->query($query);
?>

<h2>Daftar Ruang/Alat Tersedia</h2>
<table border="1">
    <tr>
        <th>Nama</th>
        <th>Tipe</th>
        <th>Jumlah</th>
        <th>Lokasi</th>
        <th>Aksi</th>
    </tr>
    <?php while ($item = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $item['nama'] ?></td>
        <td><?= $item['tipe'] ?></td>
        <td><?= $item['jumlah'] ?></td>
        <td><?= $item['lokasi'] ?></td>
        <td><a href="detail_item.php?id=<?= $item['item_id'] ?>">Detail</a></td>
    </tr>
    <?php } ?>
</table>
