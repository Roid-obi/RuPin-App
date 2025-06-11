<?php
include('../session.php');
include('../config.php');

$query = "SELECT * FROM items WHERE status = 'tersedia'";
$result = $con->query($query);
?>

<h2>Daftar Ruang/Alat Tersedia</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Gambar</th>
        <th>Nama</th>
        <th>Tipe</th>
        <th>Harga Sewa</th>
        <th>Lokasi</th>
        <th>Aksi</th>
    </tr>
    <?php while ($item = $result->fetch_assoc()) { ?>
    <tr>
        <td>
            <?php if (!empty($item['gambar'])) { ?>
                <img src="../uploads/<?= htmlspecialchars($item['gambar']) ?>" width="100" alt="Gambar <?= htmlspecialchars($item['nama']) ?>">
            <?php } else { ?>
                <em>Tidak ada gambar</em>
            <?php } ?>
        </td>
        <td><?= htmlspecialchars($item['nama']) ?></td>
        <td><?= htmlspecialchars($item['tipe']) ?></td>
        <td><?= htmlspecialchars($item['harga_sewa']) ?></td>
        <td><?= htmlspecialchars($item['lokasi']) ?></td>
        <td><a href="detail_item.php?id=<?= $item['item_id'] ?>">Detail</a></td>
    </tr>
    <?php } ?>
</table>
