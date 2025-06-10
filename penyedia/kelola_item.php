<?php
include '../session.php';
include '../config.php';

// Ambil item milik user yang login
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM items WHERE user_id = $user_id";
$result = mysqli_query($con, $query);
?>

<h2>Daftar Item Anda</h2>
<a href="tambah_item.php">+ Tambah Item</a>
<table border="1">
    <tr><th>Nama</th><th>Tipe</th><th>Jumlah</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['tipe'] ?></td>
        <td><?= $row['jumlah'] ?></td>
        <td><?= $row['lokasi'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <a href="edit_item.php?id=<?= $row['item_id'] ?>">Edit</a> |
            <a href="hapus_item.php?id=<?= $row['item_id'] ?>" onclick="return confirm('Hapus item ini?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
