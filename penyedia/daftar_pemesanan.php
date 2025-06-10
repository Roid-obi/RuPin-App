<?php
include '../session.php';
include '../config.php';
$uid = $_SESSION['user_id'];
$sql = "
SELECT p.booking_id, i.nama AS item, u.nama AS penyewa, p.status, p.tanggal 
FROM pemesanan p 
JOIN items i ON p.item_id=i.item_id 
JOIN users u ON p.user_id=u.user_id 
WHERE i.user_id=? ORDER BY p.tanggal DESC";
$stmt = $con->prepare($sql); $stmt->bind_param("i",$uid); $stmt->execute();
$res = $stmt->get_result();
?>
<table border="1">
<tr><th>Booking</th><th>Item</th><th>Penyewa</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
<?php while($r=$res->fetch_assoc()){ ?>
<tr>
  <td><?= $r['booking_id'] ?></td><td><?= $r['item'] ?></td><td><?= $r['penyewa'] ?></td>
  <td><?= $r['tanggal'] ?></td><td><?= $r['status'] ?></td>
  <td>
    <?php if($r['status']=='menunggu'){ ?>
      <a href="verifikasi_pesanan.php?id=<?= $r['booking_id'] ?>&aksi=terima">Terima</a> |
      <a href="verifikasi_pesanan.php?id=<?= $r['booking_id'] ?>&aksi=tolak">Tolak</a>
    <?php } else echo '-'; ?>
  </td>
</tr>
<?php } ?>
</table>
