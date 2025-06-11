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

<?php if (!empty($item['gambar'])): ?>
    <img src="../uploads/<?= htmlspecialchars($item['gambar']) ?>" width="300" alt="Gambar <?= htmlspecialchars($item['nama']) ?>"><br><br>
<?php else: ?>
    <p><em>Gambar tidak tersedia</em></p>
<?php endif; ?>

<p>Nama: <?= htmlspecialchars($item['nama']) ?></p>
<p>Tipe: <?= htmlspecialchars($item['tipe']) ?></p>
<p>Lokasi: <?= htmlspecialchars($item['lokasi']) ?></p>
<p>Harga Sewa: Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?></p>
<p>Status: <?= htmlspecialchars($item['status']) ?></p>

<form method="POST" action="pesan_item.php">
    <input type="hidden" name="item_id" value="<?= $item_id ?>">
    <button type="submit">Pesan Sekarang</button>
</form>
<a href="cari_item.php">Kembali</a>
