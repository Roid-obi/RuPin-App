<?php
include('../session.php');
include('../config.php');

$item_id = $_GET['id'];
$sql = "SELECT * FROM items WHERE item_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
?>

<h2>Detail Ruang / Alat</h2>
<p>Nama: <?= $item['nama'] ?></p>
<p>Tipe: <?= $item['tipe'] ?></p>
<p>Lokasi: <?= $item['lokasi'] ?></p>
<p>Jumlah Tersedia: <?= $item['jumlah'] ?></p>
<p>Status: <?= $item['status'] ?></p>

<form method="POST" action="pesan_item.php">
    <input type="hidden" name="item_id" value="<?= $item_id ?>">
    <button type="submit">Pesan Sekarang</button>
</form>
<a href="cari_item.php">Kembali</a>
